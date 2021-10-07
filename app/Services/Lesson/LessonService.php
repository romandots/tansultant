<?php
/**
 * File: LessonService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Lesson;

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
    /**
     * @var LessonRepository
     */
    private LessonRepository $repository;

    /**
     * @var CourseRepository
     */
    private CourseRepository $courseRepository;

    /**
     * @var IntentService
     */
    private IntentService $intentService;

    /**
     * @var VisitService
     */
    private $visitService;

    /**
     * LessonController constructor.
     * @param LessonRepository $repository
     * @param CourseRepository $courseRepository
     * @param IntentService $intentService
     * @param VisitService $visitService
     */
    public function __construct(
        LessonRepository $repository,
        CourseRepository $courseRepository,
        IntentService $intentService,
        VisitService $visitService
    ) {
        $this->repository = $repository;
        $this->courseRepository = $courseRepository;
        $this->intentService = $intentService;
        $this->visitService = $visitService;
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

    /**
     * Close lesson:
     * - Update intents
     * - Change status
     * @param Lesson $lesson
     * @throws Exceptions\LessonAlreadyClosedException
     * @throws Exceptions\LessonNotPassedYetException
     * @throws Exceptions\LessonNotCompletelyPaidException
     */
    public function close(Lesson $lesson): void
    {
        $now = Carbon::now();
        if ($lesson->starts_at->gt($now) || $lesson->ends_at->gt($now)) {
            throw new Exceptions\LessonNotPassedYetException();
        }

        if ($lesson->status === Lesson::STATUS_CLOSED) {
            throw new Exceptions\LessonAlreadyClosedException();
        }

        if (false === $this->visitService->visitsArePaid($lesson->visits)) {
            throw new Exceptions\LessonNotCompletelyPaidException();
        }

        $this->intentService->updateIntents($lesson->visits, $lesson->intents);

        $this->repository->close($lesson);
    }

    /**
     * @param Lesson $lesson
     * @throws Exceptions\LessonNotClosedException
     */
    public function open(Lesson $lesson): void
    {
        if ($lesson->status !== Lesson::STATUS_CLOSED) {
            throw new Exceptions\LessonNotClosedException();
        }

        $this->repository->open($lesson);
    }

    /**
     * @param Lesson $lesson
     * @throws Exceptions\LessonAlreadyCanceledException
     * @throws Exceptions\LessonAlreadyClosedException
     * @throws Exceptions\LessonHasVisitsException
     */
    public function cancel(Lesson $lesson): void
    {
        if ($lesson->status === Lesson::STATUS_CANCELED) {
            throw new Exceptions\LessonAlreadyCanceledException();
        }

        if ($lesson->status === Lesson::STATUS_CLOSED) {
            throw new Exceptions\LessonAlreadyClosedException();
        }

        if (0 !== $lesson->visits->count()) {
            throw new Exceptions\LessonHasVisitsException();
        }

        $this->repository->cancel($lesson);
    }

    /**
     * @param Lesson $lesson
     * @throws Exceptions\LessonNotCanceledYetException
     */
    public function book(Lesson $lesson): void
    {
        if ($lesson->status !== Lesson::STATUS_CANCELED) {
            throw new Exceptions\LessonNotCanceledYetException();
        }

        $this->repository->book($lesson);
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
}
