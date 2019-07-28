<?php
/**
 * File: PaymentService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Account;
use App\Models\Instructor;
use App\Models\Lesson;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use App\Models\Visit;
use App\Repository\PaymentRepository;
use App\Services\Account\AccountService;

/**
 * Class PaymentService
 * @package App\Services\Payment
 */
class PaymentService
{
    /**
     * @var PaymentRepository
     */
    private $repository;

    /**
     * @var AccountService
     */
    private $accountService;

    /**
     * PaymentService constructor.
     * @param PaymentRepository $repository
     * @param AccountService $accountService
     */
    public function __construct(PaymentRepository $repository, AccountService $accountService)
    {
        $this->repository = $repository;
        $this->accountService = $accountService;
    }

    /**
     * @param int $price
     * @param Visit $visit
     * @param Student $student
     * @param User|null $user
     * @return Payment
     * @throws \App\Services\Account\Exceptions\InsufficientFundsAccountServiceException
     */
    public function createVisitPayment(int $price, Visit $visit, Student $student, ?User $user = null): Payment
    {
        $dto = new DTO\Payment;
        $dto->type = Payment::TYPE_AUTOMATIC;
        $dto->amount = $price;
        $dto->name = \trans('payment.name_presets.visit', ['lesson' => $visit->event->name]);
        $dto->object_type = Visit::class;
        $dto->object_id = $visit->id;
        $dto->user_id = $user ? $user->id : null;

        $studentAccount = $this->accountService->getStudentAccount($student);
        $savingsAccount = $this->accountService->getSavingsAccount($visit->event->branch_id);

        $this->accountService->checkFunds($studentAccount, $price);

        [$studentPayment, $branchPayment] = $this->createInternalTransaction($dto, $studentAccount,
            $savingsAccount);

        return $branchPayment;
    }

    /**
     * @param int $price
     * @param Lesson $lesson
     * @param Instructor $instructor
     * @param User|null $user
     * @return Payment
     * @throws \App\Services\Account\Exceptions\InsufficientFundsAccountServiceException
     */
    public function createLessonPayment(int $price, Lesson $lesson, Instructor $instructor, ?User $user = null): Payment
    {
        $dto = new DTO\Payment;
        $dto->type = Payment::TYPE_AUTOMATIC;
        $dto->amount = $price;
        $dto->name = \trans('payment.name_presets.lesson', ['lesson' => $lesson->name]);
        $dto->object_type = Lesson::class;
        $dto->object_id = $lesson->id;
        $dto->user_id = $user ? $user->id : null;

        $savingsAccount = $this->accountService->getSavingsAccount($lesson->branch);
        $instructorAccount = $this->accountService->getInstructorAccount($instructor);

        $this->accountService->checkFunds($savingsAccount, $price);

        [$outgoing, $incoming] = $this->createInternalTransaction($dto, $savingsAccount, $instructorAccount);

        return $incoming;
    }

    /**
     * @param DTO\Payment $dto
     * @param Account $fromAccount
     * @param Account $toAccount
     * @return array [$fromAccountPayment, $toAccountPayment]
     */
    public function createInternalTransaction(DTO\Payment $dto, Account $fromAccount, Account $toAccount): array
    {
        $dto->transfer_type = Payment::TRANSFER_TYPE_INTERNAL;
        $dto->status = Payment::STATUS_CONFIRMED;

        $firstDto = clone $dto;
        $secondDto = clone $dto;

        $firstDto->account_id = $fromAccount->id;
        $firstDto->amount = 0 - $dto->amount;

        $secondDto->account_id = $toAccount->id;
        $secondDto->amount = $dto->amount;

        return \DB::transaction(function () use ($secondDto, $firstDto) {
            $firstPayment = $this->repository->create($firstDto);
            $secondPayment = $this->repository->create($secondDto);

            $firstPayment->related_id = $secondPayment->id;
            $secondPayment->related_id = $firstPayment->id;

            $firstPayment->save();
            $secondPayment->save();

            return [$firstPayment, $secondPayment];
        });
    }
}
