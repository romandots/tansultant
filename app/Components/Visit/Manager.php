<?php

declare(strict_types=1);

namespace App\Components\Visit;

use App\Components\Loader;
use App\Components\Visit\Entity\PriceOptions;
use App\Models\Bonus;
use App\Models\Course;
use App\Models\Enum\VisitPaymentType;
use App\Models\Student;
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
            false => $this->appendVisitSubscription($dto, $course, $priceOptions),
        };

        return $dto;
    }

    protected function appendVisitSubscription(Dto $dto, Course $course, PriceOptions $priceOptions): void
    {
        $dto->payment_type = VisitPaymentType::SUBSCRIPTION;

        // If valid subscription is already picked
        $subscriptions = Loader::subscriptions()->getStudentSubscriptionsForCourse($dto->student_id, $course->id);

        // If there's none -- let user confirm the payment
        if ($subscriptions->count() === 0) {
            $unsubscribedSubscriptions = Loader::subscriptions()
                ->getStudentPotentialSubscriptionsForCourse($dto->student_id, $course->id);

            throw new Exceptions\NoSubscriptionsException($priceOptions, $unsubscribedSubscriptions);
        }

        if (null !== $dto->subscription_id) {
            if ($subscriptions->where('id', $dto->subscription_id)->count() > 0) {
                return;
            }

            // Subscribe to course automatically
            $this->subscribeToCourseAutomatically($dto, $course);
            return;
        }

        //  If there's more than one -- let user choose
        if ($subscriptions->count() > 1) {
            throw new Exceptions\ChooseSubscriptionException($subscriptions);
        }

        // Otherwise, pick it automatically
        $dto->subscription_id = $subscriptions->first()->id;
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

    /**
     * @param Visit $visit
     * @param \App\Models\Student $student
     * @param Bonus|null $bonus
     * @param User $user
     * @return void
     */
    public function finalizeVisitPayment(Visit $visit, Student $student, Dto $dto): void
    {
        if ($dto->payment_type !== VisitPaymentType::PAYMENT) {
            $subscription = $visit->load('subscription')->subscription;

            if (null === $subscription) {
                throw new \LogicException('no_subscription_set');
            }

            Loader::subscriptions()->activatePendingSubscription($subscription, $dto->user);
            return;
        }

        $bonus = $dto->bonus_id ? Loader::bonuses()->find($dto->bonus_id) : null;

        \DB::beginTransaction();
        $payment = Loader::payments()->createVisitPayment($visit, $student, $bonus, $dto->user);
        $this->getRepository()->updatePayment($visit, $payment, null);
        \DB::commit();
    }

    protected function getService(): Service
    {
        return \app(Service::class);
    }

    /**
     * @param Dto $dto
     * @param Course $course
     * @return void
     */
    protected function subscribeToCourseAutomatically(Dto $dto, Course $course): void
    {
        $subscription = Loader::subscriptions()->find($dto->subscription_id);
        if ($dto->student_id !== $subscription->student_id) {
            $this->critical(
                'Something bad happening: attempt to subscribe another student',
                [
                    'student_id' => $dto->student_id,
                    'subscription_student_id' => $subscription->student_id,
                ]
            );
            throw new \LogicException('subscription_belongs_to_another_student');
        }
        $this->debug('Attaching course to subscription automatically', [
            'subscription_id' => $subscription->id,
            'course_id' => $course->id,
        ]);
        Loader::subscriptions()->attachCourse($subscription, $course, $dto->user);
    }
}