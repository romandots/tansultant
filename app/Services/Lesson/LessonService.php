<?php
/**
 * File: LessonService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Lesson;

use App\Http\Requests\ManagerApi\DTO\LessonsFiltered;
use App\Http\Requests\ManagerApi\DTO\StoreLesson as LessonDto;
use App\Http\Requests\PublicApi\DTO\LessonsOnDate;
use App\Jobs\GenerateLessonsOnDateJob;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Schedule;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use App\Services\Intent\IntentService;
use App\Services\Visit\VisitService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class LessonService
 * @package App\Services\Lesson
 */
class LessonService
{
    private LessonRepository $repository;
    private CourseRepository $courseRepository;

    /**
     * LessonController constructor.
     * @param LessonRepository $repository
     * @param CourseRepository $courseRepository
     * @param IntentService $intentService
     * @param VisitService $visitService
     */
    public function __construct(
        LessonRepository $repository,
        CourseRepository $courseRepository
    ) {
        $this->repository = $repository;
        $this->courseRepository = $courseRepository;
    }

    public function getRepository(): LessonRepository
    {
        return $this->repository;
    }

    private function getDateAndTime(Carbon $date, Carbon $time): Carbon
    {
        return $date->clone()
            ->setHour($time->hour)
            ->setMinute($time->minute);
    }

    public function createFromScheduleOnDate(Schedule $schedule, Carbon $date): Lesson
    {
        $startTime = $this->getDateAndTime($date, Carbon::parse($schedule->starts_at));
        $endTime =  $this->getDateAndTime($date, Carbon::parse($schedule->ends_at));

        $dto = new LessonDto();
        $dto->schedule_id = $schedule->id;
        $dto->instructor_id = $schedule->course->instructor_id;
        $dto->course_id = $schedule->course_id;
        $dto->classroom_id = $schedule->classroom_id;
        $dto->branch_id = $schedule->branch_id;
        $dto->starts_at = $startTime;
        $dto->ends_at = $endTime;
        $dto->type = Lesson::TYPE_LESSON;
        $dto->name = $this->generateCourseLessonName($schedule->course);

        return $this->repository->create($dto);
    }

    /**
     * @param LessonDto $dto
     * @return Lesson
     * @throws \Exception
     */
    public function createFromDto(LessonDto $dto): Lesson
    {
        if ($dto->type === Lesson::TYPE_LESSON) {
            $course = $this->courseRepository->find($dto->course_id);
            $dto->name = $this->generateCourseLessonName($course);
            if (null === $dto->instructor_id) {
                $dto->instructor_id = $course->instructor_id;
            }
        } else {
            $dto->name = \trans('lesson.' . Lesson::TYPE_LESSON);
        }

        return $this->repository->create($dto);
    }

    private function generateCourseLessonName(Course $course): string
    {
        return \sprintf(
            '%s %s',
            \trans('lesson.' . Lesson::TYPE_LESSON),
            $course->name
        );
    }

    public function checkIfScheduleLessonExist(Schedule $schedule, Carbon $date): bool
    {
        $startTime = $this->getDateAndTime($date, Carbon::parse($schedule->starts_at));
        $endTime =  $this->getDateAndTime($date, Carbon::parse($schedule->ends_at));

        return $this->repository->checkIfScheduleLessonExist(
            $schedule->id, $startTime->toDateTimeString(), $endTime->toDateTimeString()
        );
    }

    /**
     * @param LessonsOnDate $lessonsOnDate
     * @return Collection<Lesson>
     */
    public function getLessonsOnDate(LessonsOnDate $lessonsOnDate): Collection
    {
        $job = new GenerateLessonsOnDateJob($lessonsOnDate->date);
        dispatch($job);

        return $this->repository->getLessonsOnDate($lessonsOnDate->date);
    }

    /**
     * @param LessonsFiltered $lessonsFiltered
     * @return Collection<Lesson>
     */
    public function getLessonsFiltered(LessonsFiltered $lessonsFiltered): Collection
    {
        $job = new GenerateLessonsOnDateJob($lessonsFiltered->date);
        dispatch($job);

        return $this->repository->getLessonsFiltered($lessonsFiltered, ['instructor', 'course', 'controller']);
    }
}
