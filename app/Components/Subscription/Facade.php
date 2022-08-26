<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Common\BaseComponentFacade;
use App\Models\Course;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Subscription> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Subscription> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Subscription create(Dto $dto, array $relations = [])
 * @method \App\Models\Subscription find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\Subscription findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Subscription findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function getStudentActiveSubscriptionsForCourse(
        string $studentId,
        string $courseId,
        SubscriptionStatus $subscriptionStatus
    ): Collection {
        return $this->getRepository()->getStudentActiveSubscriptionsForCourse($studentId, $courseId, $subscriptionStatus);
    }

    public function prolongSubscription(Subscription $subscription, User $user): void
    {
        $this->getService()->prolong($subscription, $user);
    }

    /**
     * @param Subscription $subscription
     * @param iterable<Course> $courses
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function attachCourses(Subscription $subscription, iterable $courses, User $user): void
    {
        $this->getService()->attachCourses($subscription, $courses, $user);
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
        $this->getService()->detachCourses($subscription, $courses, $user);
    }
}