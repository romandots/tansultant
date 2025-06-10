<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Common\BaseComponentService;
use App\Common\Contracts;
use App\Common\Contracts\DtoWithUser;
use App\Components\Loader;
use App\Events\Subscription\SubscriptionUpdatedEvent;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Lesson;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @method Repository getRepository()
 */
class Service extends BaseComponentService
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

    /**
     * @param Dto $dto
     * @return Subscription
     * @throws \Throwable
     */
    public function create(Contracts\DtoWithUser $dto): Model
    {
        $tariff = Loader::tariffs()->find($dto->tariff_id);
        $student = Loader::students()->find($dto->student_id);
        $bonus = $dto->bonus_id ? Loader::bonuses()->find($dto->bonus_id) : null;

        $validator = $this->getValidator();
        $validator->validateTariff($tariff);
        $validator->validateStudent($student);
        $validator->validateBonus($bonus);

        $dto->name = $tariff->name;
        $dto->status = SubscriptionStatus::NOT_PAID;
        $this->addTariffValues($dto, $tariff);

        return \DB::transaction(function () use ($bonus, $student, $dto) {
            /** @var Subscription $subscription */
            $subscription = parent::create($dto);
            $payment = Loader::payments()->createSubscriptionPayment($subscription, $student, $bonus, $dto->getUser());
            $this->getRepository()->attachPayment($subscription, $payment);

            return $subscription;
        });
    }

    /**
     * For import purposes only
     * @param DtoWithUser $dto
     * @return Subscription
     * @throws \Throwable
     */
    public function createRaw(Contracts\DtoWithUser $dto): Subscription
    {
        return parent::create($dto);
    }

    /**
     * @param Subscription $record
     * @param Dto $dto
     * @return void
     * @throws \Throwable
     * @throws Exceptions\InvalidSubscriptionStatus
     */
    public function update(Model $record, DtoWithUser $dto): void
    {
        $changingTariff = $record->tariff_id !== $dto->tariff_id;

        if ($changingTariff) {
            $this->getValidator()->validateSubscriptionStatusForUpdate($record);
        }

        $tariff = Loader::tariffs()->find($dto->tariff_id);
        $student = Loader::students()->find($dto->student_id);

        $this->getValidator()->validateTariff($tariff);
        $this->getValidator()->validateStudent($student);

        $dto->name = $tariff->name;
        $dto->status = $record->status;

        if ($changingTariff) {
            $this->addTariffValues($dto, $tariff);
        } else {
            $dto->days_limit = $record->days_limit;
            $dto->courses_limit = $record->courses_limit;
            $dto->visits_limit = $record->visits_limit;
            $dto->holds_limit = $record->holds_limit;
        }

        parent::update($record, $dto);
    }

    /**
     * @param Subscription $record
     * @param \App\Models\User $user
     * @return void
     * @throws \Throwable
     * @throws Exceptions\InvalidSubscriptionStatus
     */
    public function delete(Model $record, \App\Models\User $user): void
    {
        $this->getValidator()->validateSubscriptionStatusForDelete($record);

        parent::delete($record, $user);
    }

    public function addTariffValues(Subscription|Dto $subscription, \App\Models\Tariff $tariff): void
    {
        $subscription->courses_limit = $tariff->courses_limit;
        $subscription->days_limit = $tariff->days_limit === null ? null : $subscription->days_limit + $tariff->days_limit;
        $subscription->visits_limit = $tariff->visits_limit === null ? null : $subscription->visits_limit + $tariff->visits_limit;
        $subscription->holds_limit = $tariff->holds_limit === null ? null : $subscription->holds_limit + $tariff->holds_limit;
    }

    public function dispatchEvents(iterable $subscriptions): void
    {
        foreach ($subscriptions as $subscription) {
            assert($subscription instanceof Subscription);
            try {
                $this->debug(
                    "Dispatching SubscriptionUpdatedEvent for subscription #{$subscription->id}"
                );
                SubscriptionUpdatedEvent::dispatch($subscription);
            } catch (\Exception $exception) {
                $this->error('Failed dispatching SubscriptionUpdatedEvent', $exception);
            }
        }
    }

    protected function getValidator(): Validator
    {
        return app(Validator::class);
    }

    public function getStudentSubscriptionsNotYetSubscribedOnCourse(string $studentId, string $courseId): \Illuminate\Support\Collection
    {
        $subscriptions = $this->getRepository()
            ->getStudentSubscriptionsWithCoursesLeft(
                $studentId,
                [SubscriptionStatus::ACTIVE->value, SubscriptionStatus::PENDING->value]
            );
        return $this->filterSubscriptionsNotYetSubscribedOnCourse($subscriptions, $courseId);
    }

    public function getStudentActiveSubscriptions(string $studentId): Collection
    {
        return $this->getRepository()->getStudentSubscriptions(
            $studentId,
            [SubscriptionStatus::ACTIVE->value, SubscriptionStatus::PENDING->value]
        );
    }

    public function getVisitOptionsForStudentOnLessons(string $studentId, array $lessonsIds): array
    {
        $student = Loader::students()->find($studentId);
        /** @var Lesson[] $lessons */
        $lessons = Loader::lessons()->getMany($lessonsIds);
        $subscriptions = $this->getStudentActiveSubscriptions($studentId);
        $grouped = [];
        foreach ($lessons as $lesson) {

            if ($lesson?->course->isAllowedByAgeRestrictions($student->person->birth_date?->age) === false) {
                continue;
            }

            $visitPrice = Loader::prices()->calculateLessonVisitPrice($lesson, $student);
            $existingVisit = Loader::visits()->getVisitByStudentIdAndLessonId($studentId, $lesson->id);
            $filteredSubscriptions = $lesson?->course_id !== null
                ? $this->filterSubscriptionsAvailableForCourse($subscriptions, $lesson->course_id)
                : new Collection();

            $grouped[$lesson->id] = [
                'visit_price' => $visitPrice,
                'course_subscriptions' => $filteredSubscriptions->pluck('id')->toArray(),
                'current_visit_payment_type' => $existingVisit?->payment_type?->value ?? null,
                'current_visit_id' => $existingVisit?->id,
                'current_visit_subscription_id' => $existingVisit?->subscription_id,
            ];
        }

        return $grouped;
    }

    public function getStudentSubscriptionsSubscribedOnCourse(
        string $studentId,
        string $courseId
    ): Collection {
        $subscriptions = $this->getRepository()->getStudentSubscriptionsForCourse(
            $studentId,
            $courseId,
            [SubscriptionStatus::ACTIVE->value, SubscriptionStatus::PENDING->value]
        );
        return $this->filterSubscriptionsSubscribedOnCourse($subscriptions, $courseId);
    }

    public function filterSubscriptionsNotYetSubscribedOnCourse(Collection $subscriptions, string $courseId): Collection
    {
        return $subscriptions->filter(fn (Subscription $subscription) =>
            $this->subscriptionTariffIncludesCourse($subscription, $courseId) &&
            !$this->subscriptionCoursesIncludesCourse($subscription, $courseId) &&
            $this->subscriptionHasVisits($subscription)
        );
    }

    public function filterSubscriptionsSubscribedOnCourse(Collection $subscriptions, string $courseId): Collection
    {
        return $subscriptions->filter(fn (Subscription $subscription) =>
            $this->subscriptionCoursesIncludesCourse($subscription, $courseId) &&
            $this->subscriptionHasVisits($subscription)
        );
    }

    public function filterSubscriptionsAvailableForCourse(Collection $subscriptions, string $courseId): Collection
    {
        return $subscriptions->filter(fn (Subscription $subscription) =>
            (
                $this->subscriptionTariffIncludesCourse($subscription, $courseId) ||
                $this->subscriptionCoursesIncludesCourse($subscription, $courseId)
            ) && $this->subscriptionHasVisits($subscription)
        )->unique('id');
    }

    private function subscriptionTariffIncludesCourse(Subscription $subscription, string $courseId): bool
    {
        return $subscription->load('tariff')->tariff->courses->where('id', $courseId)->isNotEmpty();
    }

    private function subscriptionCoursesIncludesCourse(Subscription $subscription, string $courseId): bool
    {
        return (bool)$subscription->load('courses')->courses->where('id', $courseId)->isNotEmpty();
    }

    private function subscriptionHasVisits(Subscription $subscription): bool
    {
        return $subscription->visits_left > 0;
    }
}