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
use App\Models\Subscription;
use App\Models\User;
use App\Models\Visit;

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

    public function createVisitPayment(Visit $visit, Student $student, ?Bonus $bonus, User $user): Payment
    {
        $price = $visit->price - ($bonus?->amount ?? 0);
        if ($price < 0) {
            throw new Exceptions\BonusIsBiggerThanPrice($bonus, $visit->price);
        }
        $name =  trans('credit.withdrawals.visit', ['visit' => $visit->name]);

        return $this->createPayment($price, $name, $student, $bonus, $user);
    }

    public function createSubscriptionPayment(Subscription $subscription, Student $student, ?Bonus $bonus, User $user): Payment
    {
        $price = $subscription->tariff->price - ($bonus?->amount ?? 0);
        if ($price < 0) {
            throw new Exceptions\BonusIsBiggerThanPrice($bonus, $subscription->tariff->price);
        }
        $name = trans('credit.withdrawals.subscription', ['subscription' => $subscription->name]);

        return $this->createPayment($price, $name, $student, $bonus, $user);
    }

    private function createPayment(int $price, string $comment, Student $student, ?Bonus $bonus, User $user): Payment
    {
        $this->validateCustomerAndBonus($student, $bonus);
        $this->validateStudentFunds($student, $price);

        return \DB::transaction(function () use ($price, $comment, $student, $bonus, $user) {
            $paymentDto = $this->buildPaymentDto($price, $comment, $student->customer, $bonus, $user);
            return $this->create($paymentDto);
        });
    }

    private function buildPaymentDto(int $price, string $name, Customer $customer, ?Bonus $bonus, User $user): Dto
    {
        $credit = Loader::credits()->createWithdrawal($customer, $price, $name, $user);
        Loader::bonuses()->activateBonus($bonus);

        $dto = new Dto($user);
        $dto->amount = 0 - $credit->amount + ($bonus?->amount ?? 0);
        $dto->name = $name;

        return $dto;
    }

    private function validateCustomerAndBonus(Student $student, ?Bonus $bonus): void
    {
        if (null !== $student->customer) {
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
}