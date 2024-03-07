<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Common\DTO\SearchFilterDto;
use App\Http\Requests\ManagerApi\DTO\SearchSubscriptionsFilterDto;
use App\Models\Course;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
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

    public function getFilterQuery(
        SearchFilterDto $filter,
        array $relations = [],
        array $countRelations = []
    ): \Illuminate\Database\Eloquent\Builder {
        $query = parent::getFilterQuery($filter, $relations, $countRelations);
        $table = Subscription::TABLE;

        assert($filter instanceof SearchSubscriptionsFilterDto);

        if ($filter->student_id) {
            $query->where("{$table}.student_id", $filter->student_id);
        }

        if ($filter->tariff_id) {
            $query->where("{$table}.tariff_id", $filter->tariff_id);
        }

        if ([] !== $filter->courses_ids) {
            $pivot = Subscription::COURSES_PIVOT_TABLE;
            $query
                ->leftJoin($pivot, "{$pivot}.subscription_id", '=', "{$table}.id")
                ->whereIn("{$pivot}.course_id", $filter->courses_ids);
        }

        if ([] !== $filter->statuses) {
            $query->whereIn("{$table}.status", $filter->statuses);
        }

        return $query
            ->orderBy("{$table}.expired_at", 'asc')
            ->orderBy("{$table}.created_at", 'asc');
    }

    public function getStudentSubscriptions(
        string $studentId,
        array $subscriptionStatuses = []
    ): Collection {
        $subscriptionStatuses = [] === $subscriptionStatuses
            ? $subscriptionStatuses : [SubscriptionStatus::ACTIVE->value, SubscriptionStatus::PENDING->value];
        $subscriptions = Subscription::TABLE;
        $pivot = Subscription::COURSES_PIVOT_TABLE;
        return $this->getQuery()
            ->join($pivot, "{$pivot}.subscription_id", '=', "{$subscriptions}.id")
            ->whereIn("{$subscriptions}.status", $subscriptionStatuses)
            ->where("{$subscriptions}.student_id", '=', $studentId)
            ->groupBy("{$subscriptions}.id", "{$pivot}.subscription_id", "{$pivot}.course_id")
            ->get();
    }

    public function getStudentSubscriptionsForCourse(
        string $studentId,
        string $courseId,
        array $subscriptionStatuses = []
    ): Collection {
        $subscriptionStatuses = [] === $subscriptionStatuses
            ? $subscriptionStatuses : [SubscriptionStatus::ACTIVE->value, SubscriptionStatus::PENDING->value];
        $subscriptions = Subscription::TABLE;
        $pivot = Subscription::COURSES_PIVOT_TABLE;
        return $this->getQuery()
            ->join($pivot, "{$pivot}.subscription_id", '=', "{$subscriptions}.id")
            ->whereIn("{$subscriptions}.status", $subscriptionStatuses)
            ->where("{$subscriptions}.student_id", '=', $studentId)
            ->where("{$pivot}.course_id", '=', $courseId)
            ->get();
    }

    public function getStudentSubscriptionsWithCoursesLeft(
        string $studentId,
        array $subscriptionStatuses
    ): Collection {
        $subscriptions = Subscription::TABLE;
        $pivot = Subscription::COURSES_PIVOT_TABLE;
        return $this->getQuery()
            ->with('tariff')
            ->whereIn("{$subscriptions}.status", $subscriptionStatuses)
            ->where("{$subscriptions}.student_id", $studentId)
            ->where(function (\Illuminate\Database\Eloquent\Builder $query) use ($pivot, $subscriptions) {
                $query
                    ->whereNull("{$subscriptions}.courses_limit")
                    ->orWhereIn("{$subscriptions}.id", function (Builder $query) use ($subscriptions, $pivot) {
                        $query
                            ->select("{$subscriptions}.id")
                            ->from($subscriptions)
                            ->leftJoin($pivot, "{$pivot}.subscription_id", '=', "{$subscriptions}.id")
                            ->groupBy("{$subscriptions}.id", "{$subscriptions}.courses_limit")
                            ->havingRaw("COUNT({$pivot}.course_id) < {$subscriptions}.courses_limit");
                    });
            })
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
        $currentStatus = $subscription->status;
        $newStatus = match ($currentStatus) {
            SubscriptionStatus::NOT_PAID, SubscriptionStatus::PENDING => SubscriptionStatus::PENDING,
            SubscriptionStatus::ACTIVE, SubscriptionStatus::EXPIRED => SubscriptionStatus::ACTIVE,
            SubscriptionStatus::ON_HOLD, SubscriptionStatus::CANCELED => throw new \Exception('not_supported'),
        };
        $this->setStatus($subscription, $newStatus);
        $this->save($subscription);
    }

    public function updateActiveSubscriptionsStatus(): Collection
    {
        $visits = Visit::TABLE;
        $subscriptions = Subscription::TABLE;
        $query = $this->getQuery()
            ->join($visits, "{$visits}.subscription_id", '=', "{$subscriptions}.id")
            ->where("{$subscriptions}.status", SubscriptionStatus::PENDING)
            ->groupBy("{$subscriptions}.id", "{$visits}.id");
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
        $subscription->expired_at?->addDays($days);
    }

    public function activate(Subscription $subscription, Carbon $activationDate, Carbon $expirationDate): void
    {
        $subscription->activated_at = $activationDate;
        $subscription->expired_at = $expirationDate;
        $this->updateStatus($subscription, SubscriptionStatus::ACTIVE);
    }

    public function deactivate(Subscription $subscription): void
    {
        $subscription->activated_at = null;
        $subscription->expired_at = null;
        $this->updateStatus($subscription, SubscriptionStatus::PENDING);
    }
}