<?php

declare(strict_types=1);

namespace App\Components\Payment;

use App\Common\BaseComponentService;
use App\Components\Bonus\Exceptions\InvalidBonusStatus;
use App\Components\Loader;
use App\Components\Student\Exceptions\StudentHasNoCustomer;
use App\Models\Bonus;
use App\Models\Customer;
use App\Models\Enum\BonusStatus;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;

/**
 * @method Repository getRepository()
 */
class Service extends BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Payment::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * @throws \Throwable
     */
    public function createPayment(int $originalPrice, string $comment, Student $student, ?Bonus $bonus, User $user): Payment
    {
        $price = $originalPrice - ($bonus?->amount ?? 0);
        if ($price < 0) {
            throw new Exceptions\BonusIsBiggerThanPrice($bonus, $originalPrice);
        }

        $this->validateCustomerAndBonus($student, $bonus);
        $this->validateStudentFunds($student, $price);

        $paymentDto = $this->buildPaymentDto($price, $comment, $student->customer, $bonus, $user);
        $payment = parent::create($paymentDto);

        return $payment;
}

    private function buildPaymentDto(int $price, string $name, Customer $customer, ?Bonus $bonus, User $user): Dto
    {
        $credit = Loader::credits()->createWithdrawal($customer, $price, $name, $user);
        if ($bonus) {
            Loader::bonuses()->activateBonus($bonus);
        }

        $dto = new Dto($user);
        $dto->amount = (0 - $credit->amount) + ($bonus?->amount ?? 0);
        $dto->name = $name;
        $dto->credit_id = $credit->id;
        $dto->bonus_id = $bonus?->id;
        return $dto;
    }

    private function validateCustomerAndBonus(Student $student, ?Bonus $bonus): void
    {
        if (null === $student->load('customer')->customer) {
            throw new StudentHasNoCustomer($student);
        }

        if (null !== $bonus && BonusStatus::PENDING !== $bonus->status) {
            throw new InvalidBonusStatus($bonus->status, [BonusStatus::PENDING]);
        }
    }

    private function validateStudentFunds(Student $student, int $price): void
    {
        if (!Loader::customers()->checkStudentFunds($student, $price)) {
            throw new Exceptions\StudentHasNotEnoughCredits($student, $price);
        }
    }

    public function delete(\Illuminate\Database\Eloquent\Model $record, \App\Models\User $user): void
    {
        assert($record instanceof Payment);
        Loader::credits()->delete($record->load('credit')->credit, $user);
        if ($record->bonus_id) {
            Loader::bonuses()->resetBonus($record->load('bonus')->bonus, $user);
        }
        parent::delete($record, $user);
    }
}