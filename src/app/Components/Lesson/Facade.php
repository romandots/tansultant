<?php

declare(strict_types=1);

namespace App\Components\Lesson;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Models\Enum\LessonStatus;
use App\Models\Lesson;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Lesson> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Lesson> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Lesson create(Dto $dto, array $relations = [])
 * @method \App\Models\Lesson find(ShowDto|string $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Lesson findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Lesson findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    protected Manager $manager;
    protected Generator $generator;

    public function __construct()
    {
        parent::__construct(Service::class);
        $this->manager = \app(Manager::class);
        $this->generator = \app(Generator::class);
    }

    public function createFromScheduleOnDate(Schedule $schedule, Carbon $date): Lesson
    {
        return $this->getService()->createFromScheduleOnDate($schedule, $date);
    }

    public function checkIfScheduleLessonExist(Schedule $schedule, Carbon $date): bool
    {
        return $this->getService()->checkIfScheduleLessonExist($schedule, $date);
    }

    public function findAndClose(string $id, User $user): Lesson
    {
        $lesson = $this->getRepository()->find($id);
        $this->manager->close($lesson, $user);

        return $lesson;
    }

    public function findAndOpen(string $id, User $user): Lesson
    {
        $lesson = $this->getRepository()->find($id);
        $this->manager->open($lesson, $user);

        return $lesson;
    }

    public function findAndCancel(string $id, User $user): Lesson
    {
        $lesson = $this->getRepository()->find($id);
        $this->manager->cancel($lesson, $user);

        return $lesson;
    }

    public function findAndBook(string $id, User $user): Lesson
    {
        $lesson = $this->getRepository()->find($id);
        $this->manager->book($lesson, $user);

        return $lesson;
    }

    public function findAndCheckout(string $id, User $user): Lesson
    {
        $lesson = $this->getRepository()->find($id);
        $this->manager->checkout($lesson, $user);

        return $lesson;
    }

    public function generateCourseLessonsOnDate(Carbon $date, string $courseId): void
    {
        $this->generator->generateCourseLessonsOnDate($date, $courseId);
    }

    public function generateLessonsOnDate(Carbon $date): void
    {
        $this->generator->generateLessonsOnDate($date);
    }

    public function updateLessonsStatuses(): int
    {
        return $this->generator->updateLessonsStatuses();
    }

    /**
     * Returns all lessons in CLOSED status of the selected instructor
     * in the selected branch in the selected period
     *
     * @param string $branchId
     * @param string $instructorId
     * @param Carbon $periodFrom
     * @param Carbon $periodTo
     * @return Collection
     */
    public function getLessonsForPayout(
        string $branchId,
        string $instructorId,
        Carbon $periodFrom,
        Carbon $periodTo
    ): Collection {
        return $this->getRepository()->getLessonsForPayout($branchId, $instructorId, $periodFrom, $periodTo);
    }

    public function setStatusBatch(iterable $lessons, LessonStatus $status, User $user): void
    {
        $this->getService()->setStatusBatch($lessons, $status, $user);
    }
}