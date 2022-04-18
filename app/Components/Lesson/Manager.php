<?php

namespace App\Components\Lesson;

use App\Models\Enum\LessonStatus;
use App\Models\Lesson;
use App\Services\WithLogger;
use Carbon\Carbon;

class Manager
{
    use WithLogger;

    protected Repository $repository;
    protected \App\Components\Intent\Facade $intents;
    protected \App\Components\Visit\Facade $visits;

    public function __construct() {
        $this->repository = \app(Repository::class);
        $this->intents = \app(\App\Components\Intent\Facade::class);
        $this->visits = \app(\App\Components\Visit\Facade::class);
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

        if ($lesson->status === LessonStatus::CLOSED) {
            throw new Exceptions\LessonAlreadyClosedException();
        }

        if (false === $this->visits->visitsArePaid($lesson->visits)) {
            throw new Exceptions\LessonNotCompletelyPaidException();
        }

        $this->intents->updateIntents($lesson->visits, $lesson->intents);

        $this->repository->close($lesson);
    }

    /**
     * @param Lesson $lesson
     * @throws Exceptions\LessonNotClosedException
     */
    public function open(Lesson $lesson): void
    {
        if ($lesson->status !== LessonStatus::CLOSED) {
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
        if ($lesson->status === LessonStatus::CANCELED) {
            throw new Exceptions\LessonAlreadyCanceledException();
        }

        if ($lesson->status === LessonStatus::CLOSED) {
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
        if ($lesson->status !== LessonStatus::CANCELED) {
            throw new Exceptions\LessonNotCanceledYetException();
        }

        $this->repository->book($lesson);
    }
}