<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Common\BaseComponentService;
use App\Components\Loader;
use App\Components\Subscription\Exceptions\InvalidSubscriptionStatus;
use App\Models\Bonus;
use App\Models\Course;
use App\Models\Enum\CourseStatus;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Hold;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;

/**
 * @method Repository getRepository()
 */
class Manager extends BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Subscription::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    public function updateSubscriptionsStatuses(): int
    {
        $this->debug('Updating subscriptions statuses');

        $active = $this->getRepository()->updateActiveSubscriptionsStatus();
        $this->debug("{$active->count()} subscriptions set to ACTIVE status");

        $onHold = $this->getRepository()->updateOhHoldSubscriptionsStatus();
        $this->debug("{$active->count()} subscriptions set to ON_HOLD status");

        $expired = $this->getRepository()->updateExpiredSubscriptionsStatus();
        $this->debug("{$expired->count()} subscriptions set to EXPIRED status");

        $subscriptions = $active->merge($onHold)->merge($expired);
        $this->getService()->dispatchEvents($subscriptions);

        return $subscriptions->count();
    }

    /**
     * @param Subscription $subscription
     * @param iterable<Course> $courses
     * @param User $user
     * @return void
     * @throws \Exception
     * @throws Exceptions\CoursesLimitReached
     * @throws Exceptions\CannotAttachDisabledCourse
     */
    public function attachCourses(Subscription $subscription, iterable $courses, User $user): void
    {
        if ($subscription->courses_limit !== null
            && $subscription->loadCount('courses')->courses_count >= $subscription->courses_limit) {
            throw new Exceptions\CoursesLimitReached($subscription->courses_limit);
        }

        $allowedCoursesIds = $subscription->tariff->load('courses')->courses->pluck('id')->toArray();
        foreach ($courses as $course) {
            if ($course->status === CourseStatus::DISABLED) {
                throw new Exceptions\CannotAttachDisabledCourse($course);
            }

            if (!\in_array($course->id, $allowedCoursesIds, true)) {
                throw new Exceptions\TariffDoesNotIncludeCourse($subscription->tariff, $course);
            }
        }

        $originalRecord = clone $subscription;
        $this->getRepository()->attachCourses($subscription, $courses);
        $this->debug("Attach courses to subscription {$subscription->name}", (array)$courses);
        $this->history->logUpdate($user, $subscription, $originalRecord);
    }

    /**
     * @param Subscription $subscription
     * @param iterable<Course> $courses
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function detachCourses(Subscription $subscription, iterable $courses, User $user): void
    {
        $originalRecord = clone $subscription;
        $this->getRepository()->detachCourses($subscription, $courses);
        $this->debug("Detach courses subscription tariff {$subscription->name}", (array)$courses);
        $this->history->logUpdate($user, $subscription, $originalRecord);
    }

    public function updateStatus(Subscription $subscription, SubscriptionStatus $status, User $user): void
    {
        match ($status) {
            SubscriptionStatus::ACTIVE => $this->activate($subscription, $user),
            SubscriptionStatus::ON_HOLD => $this->hold($subscription, $user),
            SubscriptionStatus::CANCELED => $this->cancel($subscription, $user),
            default => throw new InvalidSubscriptionStatus($subscription->status->value, [
                SubscriptionStatus::ACTIVE,
                SubscriptionStatus::ON_HOLD,
                SubscriptionStatus::CANCELED,
            ]),
        };
    }

    public function prolong(Subscription $subscription, User $user, ?Bonus $bonus): void
    {
        $this->debug("Prolonging subscription {$subscription->id}: {$subscription->name}");
        $this->getValidator()->validateSubscriptionStatusForProlong($subscription);

        $tariff = $subscription->tariff;
        $this->getValidator()->validateTariff($tariff);

        $originalRecord = clone $subscription;

        $this->getService()->addTariffValues($subscription, $tariff);
        $this->getRepository()->increaseExpiredAt($subscription, $subscription->tariff->days_limit);

        \DB::transaction(function () use ($user, $bonus, $subscription) {
            $payment = Loader::payments()->createSubscriptionProlongationPayment($subscription, $bonus, $user);
            $this->getRepository()->attachPayment($subscription, $payment);
            $this->getRepository()->save($subscription);
        });

        $this->history->logUpdate($user, $subscription, $originalRecord);
        $this->debug("Prolong subscription #{$subscription->id}");
    }

    public function activatePendingSubscription(Subscription $subscription, User $user): void
    {
        if ($subscription->status === SubscriptionStatus::PENDING) {
            $this->debug("Activating subscription {$subscription->name}");
            $this->activate($subscription, $user);
        }
    }

    protected function activate(Subscription $subscription, User $user): void
    {
        if ($subscription->status === SubscriptionStatus::PENDING) {
            $today = Carbon::today()->setTime(0, 0);
            $expirationDate = Carbon::today()->addDays(($subscription->tariff->days_limit))->setTime(23, 59);
            $this->getRepository()->setStatus(
                $subscription, SubscriptionStatus::ACTIVE, [
                    'activated_at' => $today,
                    'expired_at' => $expirationDate
                ]
            );
            $this->debug("Activated pended subscription #{$subscription->id}");
            $this->history->logActivate($user, $subscription);
            return;
        }

        if ($subscription->status === SubscriptionStatus::ON_HOLD) {
            $this->unhold($subscription, $user);
            return;
        }

        throw new Exceptions\InvalidSubscriptionStatus(
            $subscription->status->value,
            [SubscriptionStatus::PENDING, SubscriptionStatus::ON_HOLD]
        );
    }

    protected function cancel(Subscription $subscription, User $user): void
    {
        $this->getRepository()->updateStatus($subscription, SubscriptionStatus::CANCELED);
        $this->history->logCancel($user, $subscription);
    }

    protected function hold(Subscription $subscription, User $user): void
    {
        $this->debug("Holding subscription {$subscription->name}");

        $this->getValidator()->validateSubscriptionStatusForHold($subscription);

        if ($subscription->hold_id !== null) {
            throw new \LogicException('subscription_already_has_hold');
        }

        $holdDto = new \App\Components\Hold\Dto($user);
        $holdDto->subscription_id = $subscription->id;

        \DB::beginTransaction();
        $hold = Loader::holds()->create($holdDto);
        assert($hold instanceof Hold);
        $this->getRepository()->setHold($subscription, $hold);
        \DB::commit();

        $this->history->logHold($user, $subscription, $hold);
    }

    protected function unhold(Subscription $subscription, User $user): void
    {
        $this->debug("Unholding subscription {$subscription->name}");
        $this->getValidator()->validateSubscriptionStatusForUnhold($subscription);

        if ($subscription->hold_id === null) {
            throw new \LogicException('subscription_has_no_hold');
        }

        $hold = $subscription->load('active_hold')->active_hold;
        $duration = $hold->getDuration();
        $expirationDate = $subscription->expired_at->clone()->addDays($duration);

        \DB::beginTransaction();
        $this->getRepository()->unsetHold($subscription, $expirationDate);
        if ($duration) {
            $this->debug('Ending Hold');
            Loader::holds()->endHold($hold, $user);
        } else {
            $this->debug('Deleting Hold instead of ending it the same day');
            Loader::holds()->delete($hold, $user);
        }
        \DB::commit();

        $this->history->logActivate($user, $subscription);
    }

    protected function getService(): Service
    {
        return app(Service::class);
    }

    protected function getValidator(): Validator
    {
        return app(Validator::class);
    }
}