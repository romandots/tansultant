<?php

declare(strict_types=1);

namespace App\Components\Visit;

use App\Components\Loader;
use App\Events\Lesson\LessonVisitsUpdatedEvent;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Enum\VisitPaymentType;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Visit;
use App\Services\ServiceLoader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    /**
     * @param Collection<Visit> $visits
     * @return bool
     */
    public function visitsArePaid(Collection $visits): bool
    {
        foreach ($visits as $visit) {
            if ($visit->payment_type !== \App\Models\Payment::class
                || null === $visit->payment
                || null === $visit->payment->paid_at) {
                return false;
            }
        }

        return true;
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
    public function createLessonVisit(Dto $dto): Visit
    {
        $this->checkIfVisitAlreadyExists($dto->student_id, $dto->event_id);

        return \DB::transaction(function () use ($dto) {
            $lesson = Loader::lessons()->find($dto->event_id);
            $student = Loader::students()->find($dto->student_id);
            $course = $lesson->load('course')->course;
            $isCourseLesson = $course !== null;
            $subscriptions = $isCourseLesson
                ? Loader::subscriptions()->getStudentActiveSubscriptionsForCourse(
                    $student->id,
                    $course->id,
                    SubscriptionStatus::ACTIVE
                )
                : new Collection();
            $price = (int)ServiceLoader::price()->calculateLessonVisitPrice($lesson, $student);
            $isChosenSubscriptionValid = $dto->subscription_id && $subscriptions->where(
                    'id',
                    $dto->subscription_id
                )->count() === 0;

            // Pick subscription
            if ($dto->pay_from_balance) {
                $dto->payment_type = VisitPaymentType::PAYMENT;
            } else {
                $dto->payment_type = VisitPaymentType::SUBSCRIPTION;

                // Only course lessons can be paid with subscription
                if (!$isCourseLesson) {
                    throw new Exceptions\NoSubscriptionsException($price);
                }

                if ($dto->subscription_id === null || !$isChosenSubscriptionValid) {
                    $dto->subscription_id = $this->pickCompatibleSubscription($subscriptions, $price)->id;
                }
            }

            return \DB::transaction(function () use ($student, $price, $dto) {
                // Create visit
                /** @var Visit $visit */
                $visit = parent::create($dto);

                // Create payment
                if ($dto->payment_type === VisitPaymentType::PAYMENT) {
                    $payment = Loader::payments()->createVisitPayment($price, $visit, $student, $dto->getUser());
                    $visit->payment_id = $payment->id;
                    $visit->subscription_id = null;
                    $visit->save();
                }

                $this->triggerLessonVisitsUpdatedEvent($visit);

                return $visit->load('payment', 'subscription', 'student.person');
            });
        });
    }

    protected function pickCompatibleSubscription(Collection $subscriptions, int $price): Subscription
    {
        //  If there's more than one -- let user choose
        if ($subscriptions->count() > 1) {
            throw new Exceptions\ChooseSubscriptionException($subscriptions);
        }

        // If there's none -- let user confirm the payment
        if ($subscriptions->count() === 0) {
            throw new Exceptions\NoSubscriptionsException($price);
        }

        // Otherwise set the only compatible subscription
        return $subscriptions->first();
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
            Loader::payments()->findAndDelete($record->payment_id, $user);
        }
        parent::delete($record, $user);
        $this->triggerLessonVisitsUpdatedEvent($record);
    }

    protected function checkIfVisitAlreadyExists(string $student_id, string $event_id): void
    {
        try {
            $visit = $this->getRepository()->findByStudentIdAndEventId($student_id, $event_id);
            throw new Exceptions\VisitAlreadyExistsException($visit);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
        }
    }

    /**
     * @param Visit $visit
     * @return void
     */
    private function triggerLessonVisitsUpdatedEvent(Visit $visit): void
    {
        try {
            LessonVisitsUpdatedEvent::dispatch($visit->event_id);
            $this->debug('Dispatched LessonVisitsUpdated event');
        } catch (\Throwable $exception) {
            $this->error('Failed dispatching LessonVisitsUpdated event', $exception);
        }
    }
}