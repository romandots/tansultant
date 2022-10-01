<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Models\Course;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Subscription;
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

    public function getStudentActiveSubscriptionsForCourse(
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

    public function updateStatus(Subscription $subscription, SubscriptionStatus $status): void
    {
        $subscription->status = $status;
        if (SubscriptionStatus::ACTIVE === $status) {
            $this->fillDate($subscription, 'activated_at');
        }
        $this->save($subscription);
    }
}