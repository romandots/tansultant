<?php

declare(strict_types=1);

namespace App\Components\Visit;

use App\Components\Loader;
use App\Components\Visit\Entity\PriceOptions;
use App\Events\Lesson\LessonVisitsUpdatedEvent;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Enum\VisitPaymentType;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;

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
            if ($visit->payment_type !== \App\Models\Transaction::class
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

        $lesson = Loader::lessons()->find($dto->event_id);
        $student = Loader::students()->find($dto->student_id);
        $course = $lesson->load('course')->course;
        $isCourseLesson = $course !== null;
        $subscriptions = $isCourseLesson
            ? Loader::subscriptions()->getStudentSubscriptionsForCourse(
                $student->id,
                $course->id,
                SubscriptionStatus::ACTIVE
            )
            : new Collection();
        $bonuses = Loader::bonuses()->getStudentAvailableBonuses($student);
        $price = (int)($lesson->price?->price ?? 0);
        $priceOptions = $this->getPriceOptions($price, $bonuses);
        $isChosenSubscriptionValid = $dto->subscription_id
            && $subscriptions->where('id', $dto->subscription_id)->count() !== 0;

        // Pick subscription
        if ($dto->pay_from_balance) {
            $dto->payment_type = VisitPaymentType::PAYMENT;
            if (null !== $dto->bonus_id
                && !$priceOptions->bonuses->contains('id', '=', $dto->bonus_id)) {
                throw new \LogicException('bonus_id_is_invalid');
            }
        } else {
            $dto->payment_type = VisitPaymentType::SUBSCRIPTION;

            // Only course lessons can be paid with subscription
            if (!$isCourseLesson) {
                throw new \LogicException('only_course_lessons_can_be_paid_with_subscriptions');
            }

            if ($dto->subscription_id === null || !$isChosenSubscriptionValid) {
                $dto->subscription_id = $this->pickCompatibleSubscription($subscriptions, $priceOptions)->id;
            }
        }

        /** @var Visit $visit */
        $visit = parent::create($dto);

        $this->createPayment($dto, $visit, $student);
        $this->triggerLessonVisitsUpdatedEvent($visit);

        return $visit->load('payment', 'subscription', 'student.person');
    }

    protected function pickCompatibleSubscription(Collection $subscriptions, PriceOptions $priceOptions): Subscription
    {
        //  If there's more than one -- let user choose
        if ($subscriptions->count() > 1) {
            throw new Exceptions\ChooseSubscriptionException($subscriptions);
        }

        // If there's none -- let user confirm the payment
        if ($subscriptions->count() === 0) {
            throw new Exceptions\NoSubscriptionsException($priceOptions);
        }

        // Otherwise, set the only compatible subscription
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

    #[Pure] private function getPriceOptions(int $price, Collection $bonuses): Entity\PriceOptions
    {
        return new Entity\PriceOptions($price, $bonuses);
    }

    /**
     * @param Dto $dto
     * @param Visit $visit
     * @param \App\Models\Student $student
     * @param Model|null $bonus
     * @return void
     * @throws \Exception
     */
    public function createPayment(Dto $dto, Visit $visit, \App\Models\Student $student): void
    {
        try {
            if ($dto->payment_type === VisitPaymentType::PAYMENT) {
                $bonus = $dto->bonus_id ? Loader::bonuses()->findById($dto->bonus_id) : null;
                $payment = Loader::payments()->createVisitPayment($visit, $student, $bonus, $dto->getUser());

                $this->getRepository()->updatePayment($visit, $payment, null);
            }
        } catch (\Exception $exception) {
            $this->getRepository()->delete($visit);
            $this->error('Failed creating payment for visit. Deleting visit.', $exception);
            throw $exception;
        }
    }
}