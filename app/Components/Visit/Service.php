<?php

declare(strict_types=1);

namespace App\Components\Visit;

use App\Components\Loader;
use App\Events\Lesson\LessonVisitsUpdatedEvent;
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
        $this->checkIfVisitAlreadyExists($dto->student_id, $dto->event_id);

        $student = Loader::students()->find($dto->student_id);

        $dto = $this->getManager()->buildCourseLessonVisitDto($dto, $student);
        $visit = parent::create($dto);
        assert($visit instanceof Visit);

        $this->getManager()->finalizeVisitPayment($visit, $student, $dto);
        Loader::students()->activatePotentialStudent($student, $dto->user);

        $this->dispatchEvent($visit);

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
        if (null !== $record->payment_id) {
            Loader::payments()->delete($record->load('payment')->payment, $user);
        }
        parent::delete($record, $user);
        $this->dispatchEvent($record);
    }

    /**
     * @param Visit $visit
     * @return void
     */
    protected function dispatchEvent(Visit $visit): void
    {
        try {
            LessonVisitsUpdatedEvent::dispatch($visit->event_id);
            $this->debug('Dispatched LessonVisitsUpdated event');
        } catch (\Throwable $exception) {
            $this->error('Failed dispatching LessonVisitsUpdated event', $exception);
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
}