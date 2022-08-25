<?php

declare(strict_types=1);

namespace App\Components\Payment;

use App\Components\Account\Exceptions\InsufficientFundsAccountException;
use App\Models\Enum\PaymentObjectType;
use App\Models\Enum\PaymentStatus;
use App\Models\Enum\PaymentType;
use App\Models\Instructor;
use App\Models\Lesson;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use App\Models\Visit;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    protected \App\Components\Account\Facade $accounts;

    public function __construct()
    {
        parent::__construct(
            Payment::class,
            Repository::class,
            Dto::class,
            null
        );
        $this->accounts = \app(\App\Components\Account\Facade::class);
    }


    /**
     * @param int $price
     * @param Visit $visit
     * @param Student $student
     * @param User|null $user
     * @return Payment
     * @throws \App\Services\Account\Exceptions\InsufficientFundsAccountServiceException
     * @throws \Exception
     */
    public function createVisitPayment(int $price, Visit $visit, Student $student, ?User $user = null): Payment
    {
        $studentAccount = $this->accounts->getStudentAccount($student);
        $savingsAccount = $this->accounts->getSavingsAccount($visit->event->branch);

        $dto = new Dto;
        $dto->type = PaymentType::AUTO;
        $dto->object_type = PaymentObjectType::VISIT;
        $dto->amount = $price;
        $dto->name = \trans('payment.name_presets.visit', ['lesson' => $visit->event->name]);
        $dto->object_id = $visit->id;
        $dto->user_id = $user?->id;

        try {
            $this->accounts->checkFunds($studentAccount, $price);
            $dto->status = PaymentStatus::CONFIRMED;
        } catch (InsufficientFundsAccountException $insufficientFundsAccountException) {
            $dto->status = PaymentStatus::PENDING;
        }

        [, $incoming] = $this->getRepository()->createInternalTransaction($dto, $studentAccount, $savingsAccount);

        return $incoming;
    }

    /**
     * @param int $price
     * @param Lesson $lesson
     * @param Instructor $instructor
     * @param User|null $user
     * @return Payment
     * @throws \Exception
     */
    public function createLessonPayment(int $price, Lesson $lesson, Instructor $instructor, ?User $user = null): Payment
    {
        $savingsAccount = $this->accounts->getSavingsAccount($lesson->branch);
        $instructorAccount = $this->accounts->getInstructorAccount($instructor);

        $dto = new Dto;
        $dto->type = PaymentType::AUTO;
        $dto->object_type = PaymentObjectType::LESSON;
        $dto->amount = $price;
        $dto->name = \trans('payment.name_presets.lesson', ['lesson' => $lesson->name]);
        $dto->object_id = $lesson->id;
        $dto->user_id = $user?->id;

        [$outgoing, ] = $this->repository->createInternalTransaction($dto, $savingsAccount, $instructorAccount);

        return $outgoing;
    }

}