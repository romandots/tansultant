<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Models\Course;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Subscription make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Subscription> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Subscription findTrashed(string $id)
 * @method Subscription create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Subscription $record)
 * @method void restore(Subscription $record)
 * @method void forceDelete(Subscription $record)
 * @mixin \App\Common\BaseComponentRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct()
    {
        parent::__construct(
            Subscription::class,
            ['name']
        );
    }

    /**
     * @param Subscription $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->status = $dto->status;
        $record->tariff_id = $dto->tariff_id;
        $record->student_id = $dto->student_id;
        $record->days_limit = $dto->days_limit;
        $record->courses_limit = $dto->courses_limit;
        $record->visits_limit = $dto->visits_limit;
        $record->holds_limit = $dto->holds_limit;
    }

    public function getStudentSubscriptionsForCourse(
        string $studentId,
        string $courseId,
        \App\Models\Enum\SubscriptionStatus $subscriptionStatus
    ): Collection {
        $subscriptions = Subscription::TABLE;
        $pivot = 'subscription_has_courses';
        return $this->getQuery()
            ->join($pivot, "{$pivot}.subscription_id", '=', "{$subscriptions}.id")
            ->where("{$subscriptions}.status", $subscriptionStatus->value)
            ->where("{$subscriptions}.student_id", $studentId)
            ->where("{$pivot}.course_id", $courseId)
            ->get();
    }

    public function getStudentSubscriptionsWithCoursesLeft(
        string $studentId,
        \App\Models\Enum\SubscriptionStatus $subscriptionStatus
    ): Collection {
        $subscriptions = Subscription::TABLE;
        $pivot = 'subscription_has_courses';
        return $this->getQuery()
            ->join($pivot, "{$pivot}.subscription_id", '=', "{$subscriptions}.id")
            ->where("{$subscriptions}.status", $subscriptionStatus->value)
            ->where("{$subscriptions}.student_id", $studentId)
            ->where("COUNT({$pivot}.course_id)", '<', "{$subscriptions}.courses_limit")
            ->get();
    }

    /**
     * @param Subscription $subscription
     * @param iterable<Course> $courses
     * @return void
     */
    public function attachCourses(Subscription $subscription, iterable $courses): void
    {
        $this->attachRelations($subscription, 'courses', $courses, ['created_at' => Carbon::now()]);
    }

    /**
     * @param Subscription $subscription
     * @param iterable<Course> $courses
     * @return void
     */
    public function detachCourses(Subscription $subscription, iterable $courses): void
    {
        $this->detachRelations($subscription, 'courses', $courses);
    }

    public function attachPayment(Subscription $subscription, \App\Models\Payment $payment): void
    {
        $subscription->payments()->attach($payment->id, ['created_at' => Carbon::now()]);
        $this->setStatus(
            $subscription,
            SubscriptionStatus::NOT_PAID === $subscription->status ? SubscriptionStatus::PENDING : SubscriptionStatus::ACTIVE
        );
        $this->save($subscription);
    }

    public function updateActiveSubscriptionsStatus(): Collection
    {
        $visits = Visit::TABLE;
        $subscriptions = Subscription::TABLE;
        $query = $this->getQuery()
            ->leftJoin($visits, "{$visits}.subscription_id", '=', "{$subscriptions}.id")
            ->where("{$subscriptions}.status", '=', SubscriptionStatus::PENDING)
            ->where("COUNT({$visits}.id)", '>', 0);
        $collection = $query->get();
        $query->update(["{$subscriptions}.status" => SubscriptionStatus::ACTIVE]);
        return $collection;
    }

    public function updateOhHoldSubscriptionsStatus(): Collection
    {
        $query = $this->getQuery()
            ->whereNotNull('hold_id')
            ->where('status', SubscriptionStatus::ACTIVE);
        $collection = $query->get();
        $query->update(['status' => SubscriptionStatus::ON_HOLD]);
        return $collection;
    }

    public function updateExpiredSubscriptionsStatus(): Collection
    {
        $query = $this->getQuery()
            ->where('expired_at', '<=', Carbon::now())
            ->where('status', SubscriptionStatus::ACTIVE);
        $collection = $query->get();
        $query->update(['status' => SubscriptionStatus::EXPIRED]);
        return $collection;
    }

    public function setHold(Subscription $subscription, \App\Models\Hold $hold): void
    {
        $subscription->hold_id = $hold->id;
        $this->setStatus($subscription, SubscriptionStatus::ON_HOLD);
        $this->save($subscription);
    }

    public function unsetHold(Subscription $subscription, ?Carbon $expiredAt = null): void
    {
        $subscription->hold_id = null;
        $this->setStatus($subscription, SubscriptionStatus::ACTIVE, ['expired_at' => $expiredAt]);
        $this->save($subscription);
    }

    public function increaseExpiredAt(Subscription $subscription, int $days): void
    {
        $subscription->expired_at->addDays($days);
    }
}