<?php

declare(strict_types=1);

namespace App\Components\Visit;

use App\Components\Lesson\Exceptions\InvalidLessonStatusException;
use App\Components\Loader;
use App\Events\Lesson\LessonVisitsUpdatedEvent;
use App\Events\Visit\VisitEvent;
use App\Models\Enum\LessonStatus;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Visit::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    public function create(\App\Common\Contracts\DtoWithUser $dto): Model
    {
        return $this->createLessonVisit($dto);
    }

    /**
     * @param Dto $dto
     * @return Visit
     * @throws \App\Components\Account\Exceptions\InsufficientFundsAccountException|\Exception
     */
    protected function createLessonVisit(Dto $dto): Visit
    {
        $lesson = Loader::lessons()->find($dto->event_id);

        $this->validateLessonStatus($lesson);
        $this->checkIfVisitAlreadyExists($dto->student_id, $dto->event_id);

        $student = Loader::students()->find($dto->student_id);

        \DB::beginTransaction();
        $dto = $this->getManager()->buildCourseLessonVisitDto($dto, $student, $lesson);
        $visit = parent::create($dto);
        assert($visit instanceof Visit);
        $this->getManager()->finalizeVisitPayment($visit, $student, $dto);
        \DB::commit();

        $this->dispatchLessonVisitsUpdatedEvent($visit);
        $this->dispatchVisitCreatedEvent($visit, $dto->user);

        return $visit->load('payment.credit', 'payment.bonus', 'subscription', 'student.person');
    }

    /**
     * @param Visit $record
     * @param User $user
     * @return void
     * @throws \Throwable
     */
    public function delete(Model $record, \App\Models\User $user): void
    {
        $this->validateLessonStatus($record->event);

        if (null !== $record->payment_id) {
            Loader::payments()->delete($record->load('payment')->payment, $user);
        }
        parent::delete($record, $user);
        $this->dispatchLessonVisitsUpdatedEvent($record);
        $this->dispatchVisitDeletedEvent($record, $user);
    }

    protected function dispatchLessonVisitsUpdatedEvent(Visit $visit): void
    {
        try {
            LessonVisitsUpdatedEvent::dispatch($visit->event_id);
            $this->debug('Dispatched LessonVisitsUpdated event');
        } catch (\Throwable $exception) {
            $this->error('Failed dispatching LessonVisitsUpdated event', $exception);
        }
    }

    protected function dispatchVisitCreatedEvent(Visit $visit, User $user): void
    {
        try {
            VisitEvent::created($visit, $user);
            $this->debug('Dispatched VisitCreatedEvent event');
        } catch (\Throwable $exception) {
            $this->error('Failed dispatching VisitCreatedEvent event', $exception);
        }
    }

    protected function dispatchVisitDeletedEvent(Visit $visit, User $user): void
    {
        try {
            VisitEvent::deleted($visit, $user);
            $this->debug('Dispatched VisitDeletedEvent event');
        } catch (\Throwable $exception) {
            $this->error('Failed dispatching VisitDeletedEvent event', $exception);
        }
    }

    protected function checkIfVisitAlreadyExists(string $student_id, string $event_id): void
    {
        try {
            $visit = $this->getRepository()->findByStudentIdAndEventId($student_id, $event_id);
            throw new Exceptions\VisitAlreadyExistsException($visit);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
        }
    }

    protected function getManager(): Manager
    {
        return \app(Manager::class);
    }

    protected function validateLessonStatus(\App\Models\Lesson $lesson): void
    {
        $validStates = [LessonStatus::BOOKED, LessonStatus::ONGOING, LessonStatus::PASSED];
        if (!\in_array($lesson->status, $validStates, true)) {
            throw new InvalidLessonStatusException($lesson->status, $validStates);
        }
    }
}