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
use App\Models\Lesson;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use App\Services\Intent\IntentService;
use App\Services\Visit\VisitService;
use Carbon\Carbon;

/**
 * Class LessonService
 * @package App\Services\Lesson
 */
class LessonService
{
    /**
     * @var LessonRepository
     */
    private $repository;

    /**
     * @var CourseRepository
     */
    private $courseRepository;

    /**
     * @var IntentService
     */
    private $intentService;

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

    /**
     * @param LessonDto $dto
     * @return Lesson
     * @throws \Exception
     */
    public function createFromDto(LessonDto $dto): Lesson
    {
        switch ($dto->type) {
            case Lesson::TYPE_LESSON:
                $course = $this->courseRepository->find($dto->course_id);
                $name = \sprintf(
                    '%s %s',
                    \trans('lesson.' . Lesson::TYPE_LESSON),
                    $course->name
                );
                if (null === $dto->instructor_id) {
                    $dto->instructor_id = $course->instructor_id;
                }
                break;
            default:
                $name = \trans('lesson.' . Lesson::TYPE_LESSON);
        }

        return $this->repository->create($name, $dto);
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
}
