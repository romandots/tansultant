<?php

namespace App\Components\Account;

use App\Common\BaseService;
use App\Models\{Account, Bonus, Branch, Enum\BonusStatus, Instructor, Payment, Student};

/**
 * @method Repository getRepository()
 */
class Service extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            Account::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * @param Student $student
     * @return Account
     * @throws \Exception
     */
    public function getStudentAccount(Student $student): Account
    {
        try {
            return $this->getRepository()->findStudentPersonalAccountByOwnerId($student->id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->getRepository()->createStudentPersonalAccount($student);
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
            return $this->getRepository()->findInstructorPersonalAccountByOwnerId($instructor->id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->getRepository()->createInstructorPersonalAccount($instructor);
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
            return $this->getRepository()->findBranchOperationalAccountByOwnerId($branch->id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->getRepository()->createBranchOperationalAccount($branch);
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
            return $this->getRepository()->findBranchSavingsAccountByOwnerId($branch->id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->getRepository()->createBranchSavingsAccount($branch);
        }
    }

    /**
     * @param Account $account
     * @param int $amount
     * @throws Exceptions\InsufficientFundsAccountException
     */
    public function checkFunds(Account $account, int $amount): void
    {
        $availableAmount = $this->getTotalAmount($account);
        if ($availableAmount < $amount) {
            throw new Exceptions\InsufficientFundsAccountException($account, $availableAmount, $amount);
        }
    }

    /**
     * @param Account $account
     * @return int
     */
    public function getAmount(Account $account): int
    {
        return $account->payments
            ->where('status', PaymentStatus::CONFIRMED)
            ->sum('amount');
    }

    /**
     * @param Account $account
     * @return int
     */
    public function getBonusAmount(Account $account): int
    {
        return $account->bonuses
            ->where('status', BonusStatus::PENDING)
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