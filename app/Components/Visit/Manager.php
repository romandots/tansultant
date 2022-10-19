<?php

declare(strict_types=1);

namespace App\Components\Visit;

use App\Components\Loader;
use App\Components\Visit\Entity\PriceOptions;
use App\Models\Bonus;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Enum\VisitPaymentType;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Support\Collection;

/**
 * @method Repository getRepository()
 */
class Manager extends \App\Common\BaseComponentService
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
            if (
                ($visit->payment_type === VisitPaymentType::PAYMENT && $visit->payment_id === null)
                || ($visit->payment_type === VisitPaymentType::SUBSCRIPTION && $visit->subscription_id === null)
            ) {
                return false;
            }
        }

        return true;
    }

    public function buildCourseLessonVisitDto(Dto $dto, Student $student): Dto
    {
        $lesson = Loader::lessons()->find($dto->event_id);
        $course = $lesson->load('course')->course;

        if ($course === null) {
            throw new \LogicException('unsupported_lesson_type');
        }

        $bonuses = Loader::bonuses()->getStudentAvailableBonuses($student);

        $price = (int)($lesson->price?->price ?? 0);
        $priceOptions = new Entity\PriceOptions($price, $bonuses);

        match ($dto->pay_from_balance) {
            true => $this->appendVisitPayment($dto, $priceOptions),
            false => $this->appendVisitSubscription($dto, $priceOptions),
        };

        return $dto;
    }

    protected function appendVisitPayment(Dto $dto, PriceOptions $priceOptions): void
    {
        $dto->payment_type = VisitPaymentType::PAYMENT;
        $dto->price = $priceOptions->price;
        if (null !== $dto->bonus_id
            && !$priceOptions->bonuses->contains('id', '=', $dto->bonus_id)) {
            throw new \LogicException('bonus_id_is_invalid');
        }
    }

    protected function appendVisitSubscription(Dto $dto, PriceOptions $priceOptions): void
    {
        $subscriptions = Loader::subscriptions()->getStudentSubscriptionsForCourse(
            $dto->student_id,
            $dto->event_id,
            SubscriptionStatus::ACTIVE
        );

        $isChosenSubscriptionValid = $dto->subscription_id
            && $subscriptions->where('id', $dto->subscription_id)->count() > 0;
        if (!$isChosenSubscriptionValid || $dto->subscription_id === null) {
            $dto->subscription_id = $this->pickCompatibleSubscription($subscriptions, $priceOptions)->id;
        }

        $dto->payment_type = VisitPaymentType::SUBSCRIPTION;
    }

    /**
     * @param Visit $visit
     * @param \App\Models\Student $student
     * @param Bonus|null $bonus
     * @param User $user
     * @return void
     */
    public function createPayment(Visit $visit, Student $student, Dto $dto): void
    {
        if ($dto->payment_type !== VisitPaymentType::PAYMENT) {
            return;
        }
        $bonus = $dto->bonus_id ? Loader::bonuses()->findById($dto->bonus_id) : null;

        \DB::beginTransaction();
        $payment = Loader::payments()->createVisitPayment($visit, $student, $bonus, $dto->user);
        $this->getRepository()->updatePayment($visit, $payment, null);
        \DB::commit();
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


    protected function getService(): Service
    {
        return \app(Service::class);
    }
}