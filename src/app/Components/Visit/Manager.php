<?php

declare(strict_types=1);

namespace App\Components\Visit;

use App\Components\Loader;
use App\Components\Subscription\Exceptions\InvalidSubscriptionStatus;
use App\Components\Subscription\Exceptions\VisitsLimitReached;
use App\Components\Visit\Entity\PriceOptions;
use App\Models\Course;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Enum\VisitPaymentType;
use App\Models\Lesson;
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

    public function buildCourseLessonVisitDto(Dto $dto, Student $student, Lesson $lesson): Dto
    {
        $course = $lesson->load('course')->course;

        if ($course === null) {
            throw new \LogicException('unsupported_lesson_type');
        }

        $bonuses = Loader::bonuses()->getStudentAvailableBonuses($student);

        $price = (int)Loader::prices()->calculateLessonVisitPrice($lesson, $student);
        $priceOptions = new Entity\PriceOptions($price, $bonuses);

        if ($dto->pay_from_balance) {
            $this->appendVisitPayment($dto, $priceOptions);
            return $dto;
        }

        $subscription = $this->appendVisitSubscription($dto, $course, $priceOptions);
        $this->validateSubscription($subscription);

        return $dto;
    }

    protected function appendVisitSubscription(Dto $dto, Course $course, PriceOptions $priceOptions): Subscription
    {
        $dto->payment_type = VisitPaymentType::SUBSCRIPTION;

        // If valid subscription is already picked
        $priceOptions->subscriptionsWithCourse = Loader::subscriptions()->getStudentSubscriptionsForCourse($dto->student_id, $course->id);
        $priceOptions->subscriptionsWithoutCourse = Loader::subscriptions()
            ->getStudentPotentialSubscriptionsForCourse($dto->student_id, $course->id)
            ->filter(fn (Subscription $subscription) => $subscription->visits_left > 0);

        if ($dto->subscription_id) {
            $subscription = $priceOptions->subscriptionsWithCourse->where('id', $dto->subscription_id)->first();
            if (null !== $subscription) {
                return $subscription;
            }

            $subscription = $priceOptions->subscriptionsWithoutCourse->where('id', $dto->subscription_id)->first();
            if (null === $subscription) {
                throw new \LogicException('subscription_belongs_to_another_student');
            }

            // Subscribe to course automatically
             if ($dto->student_id === $subscription->student_id) {
                 $this->subscribeToCourseAutomatically($subscription, $course, $dto->user);
            }
            return $subscription;
        }

        // If there's none -- let user confirm the payment
        if ($priceOptions->subscriptionsWithCourse->count() === 0) {;
            throw new Exceptions\NoSubscriptionsException($priceOptions);
        }

        //  If there's more than one -- let user choose
        if ($priceOptions->subscriptionsWithCourse->count() > 1) {
            throw new Exceptions\ChooseSubscriptionException($priceOptions);
        }

        // Otherwise, pick it automatically
        $dto->subscription_id = $priceOptions->subscriptionsWithCourse->first()->id;

        return $priceOptions->subscriptionsWithCourse->first();
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

    public function finalizeVisitPayment(Visit $visit, Student $student, Dto $dto): void
    {
        if ($dto->payment_type === VisitPaymentType::SUBSCRIPTION) {
            $subscription = $visit->load('subscription')->subscription;

            if (null === $subscription) {
                throw new \LogicException('no_subscription_set');
            }
            return;
        }

        $bonus = $dto->bonus_id ? Loader::bonuses()->find($dto->bonus_id) : null;

        $payment = Loader::payments()->createVisitPayment($visit, $student, $bonus, $dto->user);
        $this->getRepository()->updatePayment($visit, $payment, null);
    }

    protected function getService(): Service
    {
        return \app(Service::class);
    }

    protected function subscribeToCourseAutomatically(Subscription $subscription, Course $course, User $user): void
    {
        $this->debug('Attaching course to subscription automatically', [
            'subscription_id' => $subscription->id,
            'course_id' => $course->id,
        ]);
        Loader::subscriptions()->attachCourse($subscription, $course, $user);
    }

    protected function validateSubscription(?\App\Models\Subscription $subscription): void
    {
        if (null === $subscription) {
            return;
        }

        $allowedStatuses = [SubscriptionStatus::ACTIVE, SubscriptionStatus::PENDING];
        if (!\in_array($subscription->status, $allowedStatuses, true)) {
            throw new InvalidSubscriptionStatus($subscription->status->value, $allowedStatuses);
        }

        $subscription->loadCount('visits');
        if (null !== $subscription->visits_left && $subscription->visits_left <= 0) {
            throw new VisitsLimitReached($subscription->visits_limit);
        }
    }
}