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
        $this->getValidator()->validateSubscriptionStatusForAttachCourses($subscription);

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

    public function updateStatus(Subscription $subscription, SubscriptionStatus $toStatus, User $user): void
    {
        $subscription->refresh();
        $this->getValidator()->validateSubscriptionStatusForTransition($subscription, $toStatus);
        match ($toStatus) {
            SubscriptionStatus::ACTIVE => $this->endOrDeleteHold($subscription, $user),
            SubscriptionStatus::ON_HOLD => $this->createHold($subscription, $user),
            SubscriptionStatus::CANCELED => $this->cancel($subscription, $user),
            default => throw new InvalidSubscriptionStatus($subscription->status->value, [
                SubscriptionStatus::ACTIVE,
                SubscriptionStatus::ON_HOLD,
                SubscriptionStatus::CANCELED,
            ]),
        };
        $subscription->refresh();
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

    public function activate(Subscription $subscription, User $user): void
    {
        if ($subscription->status !== SubscriptionStatus::PENDING) {
            throw new Exceptions\InvalidSubscriptionStatus(
                $subscription->status->value,
                [SubscriptionStatus::PENDING, SubscriptionStatus::ON_HOLD]
            );
        }

        $today = Carbon::today()->setTime(0, 0);
        $expirationDate = Carbon::today()->addDays(($subscription->tariff->days_limit))->setTime(23, 59);
        $this->getRepository()->activate($subscription, $today, $expirationDate);
        $this->debug("Activated pended subscription #{$subscription->id}");
        $this->history->logActivate($user, $subscription);
    }

    public function deactivate(Subscription $subscription, User $user): void
    {
        if ($subscription->status !== SubscriptionStatus::ACTIVE) {
            throw new Exceptions\InvalidSubscriptionStatus(
                $subscription->status->value,
                [SubscriptionStatus::ACTIVE]
            );
        }

        $this->getRepository()->deactivate($subscription);
        $this->debug("Deactivated subscription #{$subscription->id}");
        $this->history->logDeactivate($user, $subscription);
    }

    protected function cancel(Subscription $subscription, User $user): void
    {
        $this->getValidator()->validateSubscriptionStatusForCancel($subscription);
        $this->getRepository()->updateStatus($subscription, SubscriptionStatus::CANCELED);
        $this->history->logCancel($user, $subscription);
    }

    public function hold(Subscription $subscription, Hold $hold, User $user): void
    {
        $this->debug("Holding subscription {$subscription->name}");
        $this->getRepository()->setHold($subscription, $hold);
        $this->history->logHold($user, $subscription, $hold);
    }

    public function unhold(Subscription $subscription, int $duration, User $user): void
    {
        $this->debug("Unholding subscription {$subscription->name}");
        $expirationDate = $subscription->expired_at?->clone()->addDays($duration);
        $this->getRepository()->unsetHold($subscription, $expirationDate);
        $this->history->logActivate($user, $subscription);
    }

    protected function createHold(Subscription $subscription, User $user): Hold
    {
        $this->getValidator()->validateSubscriptionStatusForHold($subscription);
        return Loader::holds()->createHoldForSubscription($subscription, $user);
    }

    protected function endOrDeleteHold(Subscription $subscription, User $user): void
    {
        $this->getValidator()->validateSubscriptionStatusForUnhold($subscription);

        if ($subscription->load('active_hold')->active_hold === null) {
            return;
        }

        Loader::holds()->endOrDeleteHold($subscription->active_hold, $user);
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