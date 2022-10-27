<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Common\BaseComponentFacade;
use App\Common\DTO\IdsDto;
use App\Common\DTO\ShowDto;
use App\Components\Loader;
use App\Models\Course;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Hold;
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
 * @method \App\Models\Subscription find(ShowDto|string $showDto)
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

    protected function getManager(): Manager
    {
        return app(Manager::class);
    }

    protected function getValidator(): Validator
    {
        return app(Validator::class);
    }

    public function getStudentSubscriptionsForCourse(string $studentId, string $courseId): Collection
    {
        return $this->getService()->getStudentSubscriptionsForCourse($studentId, $courseId);
    }

    public function getStudentPotentialSubscriptionsForCourse(string $studentId, string $courseId): Collection
    {
        return $this->getService()->getStudentPotentialSubscriptionsForCourse($studentId, $courseId);
    }

    public function findAndProlong(ProlongDto $dto): Subscription
    {
        $subscription = $this->find($dto->id);
        $bonus = $dto->bonus_id ? Loader::bonuses()->getById($dto->bonus_id) : null;
        $this->getManager()->prolong($subscription, $dto->user, $bonus);

        return $subscription->load($dto->with)->loadCount($dto->with_count);
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

    public function attachCourse(Subscription $subscription, Course $course, User $user): void
    {
        $this->getManager()->attachCourses($subscription, [$course], $user);
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
        $subscription = $this->getService()->find($statusDto->id, $statusDto->with, $statusDto->with_count, true);
        assert($subscription instanceof Subscription);
        $this->getManager()->updateStatus($subscription, $statusDto->status, $statusDto->user);

        return $subscription->load($statusDto->with)->loadCount($statusDto->with_count);
    }

    public function activatePendingSubscription(Subscription $subscription, User $user): void
    {
        $this->getManager()->activate($subscription, $user);
    }

    public function deactivateActiveSubscription(Subscription $subscription, User $user): void
    {
        $this->getManager()->deactivate($subscription, $user);
    }

    public function hold(Subscription $subscription, Hold $hold, User $user): void
    {
        $this->getManager()->hold($subscription, $hold, $user);
    }

    public function unhold(Subscription $subscription, int $duration, User $user): void
    {
        $this->getManager()->unhold($subscription, $duration, $user);
    }

    public function getAllowedStatusesFor(SubscriptionStatus $status): array
    {
        return $this->getValidator()->getAllowedDestinations($status);
    }

    public function canTransit(SubscriptionStatus $from, SubscriptionStatus $to): bool
    {
        return $this->getValidator()->canTransit($from, $to);
    }

    public function canBeCanceled(Subscription $subscription): bool
    {
        return $this->getValidator()->canBeCanceled($subscription);
    }

    public function canBeDeleted(Subscription $subscription): bool
    {
        return $this->getValidator()->canBeDeleted($subscription);
    }

    public function canBeUpdated(Subscription $subscription): bool
    {
        return $this->getValidator()->canBeUpdated($subscription);
    }

    public function canBeProlonged(Subscription $subscription): bool
    {
        return $this->getValidator()->canBeProlonged($subscription);
    }

    public function canBePaused(Subscription $subscription): bool
    {
        return $this->getValidator()->canBePaused($subscription);
    }

    public function canBeUnpaused(Subscription $subscription): bool
    {
        return $this->getValidator()->canBeUnpaused($subscription);
    }
}