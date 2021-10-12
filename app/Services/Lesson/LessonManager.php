<?php

namespace App\Services\Lesson;

use App\Models\Lesson;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use App\Services\BaseService;
use App\Services\Intent\IntentService;
use App\Services\Visit\VisitService;
use App\Services\WithLogger;
use Carbon\Carbon;

class LessonManager
{
    use WithLogger;

    private LessonRepository $repository;
    private IntentService $intentService;
    private VisitService $visitService;

    /**
     * LessonController constructor.
     * @param LessonRepository $repository
     * @param CourseRepository $courseRepository
     * @param IntentService $intentService
     * @param VisitService $visitService
     */
    public function __construct(
        LessonRepository $repository,
        IntentService $intentService,
        VisitService $visitService
    ) {
        $this->repository = $repository;
        $this->intentService = $intentService;
        $this->visitService = $visitService;
    }

    protected function getLoggerPrefix(): string
    {
        return __CLASS__;
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