<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Common\BaseComponentFacade;
use App\Common\DTO\IdsDto;
use App\Common\DTO\ShowDto;
use App\Components\Loader;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Subscription> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Subscription> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Subscription create(Dto $dto, array $relations = [])
 * @method \App\Models\Subscription find(ShowDto $showDto)
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

    public function getStudentSubscriptionsForCourse(
        string $studentId,
        string $courseId,
        SubscriptionStatus $subscriptionStatus
    ): Collection {
        return $this->getRepository()->getStudentSubscriptionsForCourse($studentId, $courseId, $subscriptionStatus);
    }

    public function prolongSubscription(Subscription $subscription, User $user): void
    {
        $this->getManager()->prolong($subscription, $user);
    }

    /**
     * @param IdsDto $dto
     * @return Subscription
     * @throws \Exception
     */
    public function findAndAttachCourses(IdsDto $dto): Subscription
    {
        /** @var Subscription $subscription */
        $subscription = $this->getRepository()->find($dto->id);
        $courses = Loader::courses()->getMany($dto->relations_ids);
        $this->getManager()->attachCourses($subscription, $courses, $dto->user);

        return $subscription
            ->load($dto->with)
            ->loadCount($dto->with_count);
    }

    /**
     * @param IdsDto $dto
     * @return Subscription
     * @throws \Exception
     */
    public function findAndDetachCourses(IdsDto $dto): Subscription
    {
        /** @var Subscription $subscription */
        $subscription = $this->getRepository()->find($dto->id);
        $courses = Loader::courses()->getMany($dto->relations_ids);
        $this->getManager()->detachCourses($subscription, $courses, $dto->user);

        return $subscription
            ->load($dto->with)
            ->loadCount($dto->with_count);
    }

    public function updateSubscriptionsStatuses(): int
    {
        return $this->getManager()->updateSubscriptionsStatuses();
    }

    public function updateStatus(StatusDto $statusDto): Subscription
    {
        $subscription = $this->getService()->find($statusDto->id);
        assert($subscription instanceof Subscription);
        $this->getManager()->updateStatus($subscription, $statusDto->status, $statusDto->user);

        return $subscription;
    }

    protected function getManager(): Manager
    {
        return app(Manager::class);
    }
}