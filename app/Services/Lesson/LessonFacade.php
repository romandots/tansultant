<?php

namespace App\Services\Lesson;

use App\Http\Requests\ManagerApi\DTO\GetLessonsOnDate;
use App\Http\Requests\ManagerApi\DTO\StoreLesson;
use App\Models\Course;
use App\Models\Lesson;
use App\Repository\LessonRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LessonFacade
{
    protected LessonRepository $repository;
    protected LessonService $service;
    protected LessonGenerator $generator;

    public function __construct(LessonRepository $repository, LessonService $service, LessonGenerator $generator)
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->generator = $generator;
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
     * @param GetLessonsOnDate $getLessonsOnDate
     * @return Collection<Lesson>
     */
    public function getLessonsForDate(GetLessonsOnDate $getLessonsOnDate): Collection
    {
        return $this->repository
            ->getLessonsForDate($getLessonsOnDate)
            ->load('instructor', 'course', 'controller');
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
}