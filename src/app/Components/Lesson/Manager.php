<?php

namespace App\Components\Lesson;

use App\Common\Traits\WithLogger;
use App\Components\Loader;
use App\Events\Lesson\LessonStatusUpdatedEvent;
use App\Models\Enum\LessonStatus;
use App\Models\Lesson;
use App\Models\User;

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
    public function close(Lesson $lesson, User $user): void
    {
        $this->validateStatus($lesson, [
            LessonStatus::PASSED,
        ]);

        if (false === $this->visits->visitsArePaid($lesson->visits)) {
            throw new Exceptions\LessonNotCompletelyPaidException();
        }

        \DB::transaction(function () use ($user, $lesson) {
            $this->intents->updateIntents($lesson->visits, $lesson->intents);
            $this->repository->close($lesson);

            Loader::logRecords()->logClose($user, $lesson);

            $this->debug("Close lesson {$lesson->id}");
            $this->triggerLessonStatusUpdatedEvent($lesson);
        });
    }

    public function open(Lesson $lesson, User $user): void
    {
        $this->validateStatus($lesson, [
            LessonStatus::CLOSED,
        ]);

        \DB::transaction(function () use ($user, $lesson) {
            $this->repository->open($lesson);

            Loader::logRecords()->logOpen($user, $lesson);

            $this->debug("Opened lesson {$lesson->id}");
            $this->triggerLessonStatusUpdatedEvent($lesson);
        });
    }

    public function cancel(Lesson $lesson, User $user): void
    {
        $this->validateStatus($lesson, [
            LessonStatus::BOOKED,
            LessonStatus::PASSED,
            LessonStatus::ONGOING,
        ]);
        if ($lesson->status === LessonStatus::CANCELED) {
            throw new Exceptions\LessonAlreadyCanceledException();
        }

        if ($lesson->status === LessonStatus::CLOSED) {
            throw new Exceptions\LessonAlreadyClosedException();
        }

        if (0 !== $lesson->visits->count()) {
            throw new Exceptions\LessonHasVisitsException();
        }

        \DB::transaction(function () use ($user, $lesson) {
            $this->repository->cancel($lesson);

            Loader::logRecords()->logCancel($user, $lesson);

            $this->debug("Canceled lesson {$lesson->id}");
            $this->triggerLessonStatusUpdatedEvent($lesson);
        });
    }

    public function book(Lesson $lesson, User $user): void
    {
        $this->validateStatus($lesson, [
            LessonStatus::CANCELED
        ]);

        \DB::transaction(function () use ($user, $lesson) {
            $this->repository->book($lesson);

            Loader::logRecords()->logBook($user, $lesson);

            $this->debug("Booked lesson {$lesson->id}");
            $this->triggerLessonStatusUpdatedEvent($lesson);
        });
    }

    public function checkout(Lesson $lesson, User $user): void
    {
        $this->validateStatus($lesson, [
            LessonStatus::CLOSED
        ]);

        \DB::transaction(function () use ($user, $lesson) {

            // @todo Create transactions

            $this->repository->checkout($lesson);

            Loader::logRecords()->logCheckout($user, $lesson);

            $this->debug("Checked out lesson {$lesson->id}");
            $this->triggerLessonStatusUpdatedEvent($lesson);
        });
    }

    private function triggerLessonStatusUpdatedEvent(Lesson $lesson): void
    {
        LessonStatusUpdatedEvent::dispatch($lesson->id);
        $this->debug('Dispatched LessonStatusUpdated event');
    }

    private function validateStatus(Lesson $lesson, array $validStatuses): void
    {
        if (!\in_array($lesson->status, $validStatuses, true)) {
            throw new Exceptions\InvalidLessonStatusException($lesson->status, $validStatuses);
        }
    }
}