<?php

declare(strict_types=1);

namespace App\Components\Lesson;

use App\Common\BaseComponentFacade;
use App\Models\Lesson;
use App\Models\Schedule;
use Carbon\Carbon;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Lesson> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Lesson> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Lesson create(Dto $dto, array $relations = [])
 * @method \App\Models\Lesson find(string $id, array $relations = [])
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

    public function findAndClose(string $id): Lesson
    {
        $lesson = $this->getRepository()->find($id);
        $this->manager->close($lesson);

        return $lesson;
    }

    public function findAndOpen(string $id): Lesson
    {
        $lesson = $this->getRepository()->find($id);
        $this->manager->open($lesson);

        return $lesson;
    }

    public function findAndCancel(string $id): Lesson
    {
        $lesson = $this->getRepository()->find($id);
        $this->manager->cancel($lesson);

        return $lesson;
    }

    public function findAndBook(string $id): Lesson
    {
        $lesson = $this->getRepository()->find($id);
        $this->manager->book($lesson);

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
}