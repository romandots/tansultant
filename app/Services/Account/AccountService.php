<?php
/**
 * File: AccountService.inc
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Account;

use App\Models\Account;
use App\Models\Bonus;
use App\Models\Branch;
use App\Models\Instructor;
use App\Models\Payment;
use App\Models\Student;
use App\Repository\AccountRepository;

/**
 * Class AccountService
 * @package App\Services\Account
 */
class AccountService
{
    /**
     * @var AccountRepository
     */
    private $repository;

    /**
     * AccountService constructor.
     * @param AccountRepository $repository
     */
    public function __construct(AccountRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Student $student
     * @return Account
     * @throws \Exception
     */
    public function getStudentAccount(Student $student): Account
    {
        try {
            return $this->repository->findStudentPersonalAccountByOwnerId($student->id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->repository->createStudentPersonalAccount($student);
        }
    }

    /**
     * @param Instructor $instructor
     * @return Account
     * @throws \Exception
     */
    public function getInstructorAccount(Instructor $instructor): Account
    {
        try {
            return $this->repository->findInstructorPersonalAccountByOwnerId($instructor->id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->repository->createInstructorPersonalAccount($instructor);
        }
    }

    /**
     * @param Branch $branch
     * @return Account
     * @throws \Exception
     */
    public function getOperationalAccount(Branch $branch): Account
    {
        try {
            return $this->repository->findBranchOperationalAccountByOwnerId($branch->id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->repository->createBranchOperationalAccount($branch);
        }
    }

    /**
     * @param Branch $branch
     * @return Account
     * @throws \Exception
     */
    public function getSavingsAccount(Branch $branch): Account
    {
        try {
            return $this->repository->findBranchSavingsAccountByOwnerId($branch->id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->repository->createBranchSavingsAccount($branch);
        }
    }

    /**
     * @param Account $account
     * @param int $amount
     * @throws Exceptions\InsufficientFundsAccountServiceException
     */
    public function checkFunds(Account $account, int $amount): void
    {
        $availableAmount = $this->getTotalAmount($account);
        if ($availableAmount < $amount) {
            throw new Exceptions\InsufficientFundsAccountServiceException($account, $availableAmount, $amount);
        }
    }

    /**
     * @param Account $account
     * @return int
     */
    public function getAmount(Account $account): int
    {
        return $account->payments
            ->where('status', Payment::STATUS_CONFIRMED)
            ->sum('amount');
    }

    /**
     * @param Account $account
     * @return int
     */
    public function getBonusAmount(Account $account): int
    {
        return $account->bonuses
            ->where('status', Bonus::STATUS_PENDING)
            ->sum('amount');
    }

    /**
     * @param Account $account
     * @return int
     */
    public function getTotalAmount(Account $account): int
    {
        return $this->getAmount($account) + $this->getBonusAmount($account);
    }
}
