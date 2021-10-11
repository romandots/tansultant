<?php

namespace App\Services\Lesson;

use App\Http\Requests\DTO\Contracts\PaginatedInterface;
use App\Http\Requests\ManagerApi\DTO\LessonsFiltered;
use App\Http\Requests\ManagerApi\DTO\StoreLesson;
use App\Http\Requests\PublicApi\DTO\LessonsOnDate;
use App\Models\Lesson;
use App\Repository\LessonRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LessonFacade
{
    protected LessonRepository $repository;
    protected LessonService $service;
    protected LessonGenerator $generator;
    protected LessonManager $manager;

    public function __construct(
        LessonRepository $repository,
        LessonService    $service,
        LessonGenerator  $generator,
        LessonManager    $manager
    )
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->generator = $generator;
        $this->manager = $manager;
    }

    public function find(string $id): Lesson
    {
        return $this->repository->find($id)->load('instructor', 'course', 'controller');
    }

    public function createFromDto(StoreLesson $storeLesson): Lesson
    {
        return $this->service
            ->createFromDto($storeLesson)
            ->load('instructor', 'course', 'controller');
    }

    public function findAndUpdate(string $id, StoreLesson $storeLesson): Lesson
    {
        $lesson = $this->repository->find($id);
        $this->repository->update($lesson, $storeLesson);
        $lesson->load('instructor', 'course', 'controller');

        return $lesson;
    }

    public function findAndChangeInstructor(string $id, string $instructorId): Lesson
    {
        $lesson = $this->repository->find($id);
        $this->repository->updateInstructor($lesson, $instructorId);
        $lesson->load('instructor', 'course', 'controller');

        return $lesson;
    }

    public function findAndDelete(string $id): void
    {
        $lesson = $this->repository->find($id);
        $this->repository->delete($lesson);
    }

    /**
     * @param LessonsFiltered $lessonsFiltered
     * @return Collection<Lesson>
     */
    public function getLessonsFiltered(LessonsFiltered $lessonsFiltered): Collection
    {
        return $this->service->getLessonsFiltered($lessonsFiltered);
    }

    public function getLessonsOnDate(LessonsOnDate $lessonsOnDate): Collection
    {
        return $this->service->getLessonsOnDate($lessonsOnDate);
    }

    public function findAndClose(string $id): Lesson
    {
        $lesson = $this->repository->find($id);
        $this->service->close($lesson);

        return $lesson;
    }

    public function findAndOpen(string $id): Lesson
    {
        $lesson = $this->repository->find($id);
        $this->service->open($lesson);

        return $lesson;
    }

    public function findAndCancel(string $id): Lesson
    {
        $lesson = $this->repository->find($id);
        $this->service->cancel($lesson);

        return $lesson;
    }

    public function findAndBook(string $id): Lesson
    {
        $lesson = $this->repository->find($id);
        $this->repository->book($lesson);

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

    public function search(PaginatedInterface $searchParams, array $relations = []): Collection
    {
        return $this->service->search($searchParams, $relations);
    }

    public function getMeta(PaginatedInterface $searchParams): array
    {
        return $this->service->getMeta($searchParams);
    }
}