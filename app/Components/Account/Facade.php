<?php

declare(strict_types=1);

namespace App\Components\Account;

use App\Common\BaseComponentFacade;
use App\Models\Account;
use App\Models\Branch;
use App\Models\Instructor;
use App\Models\Student;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Account> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Account> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\Contracts\PaginatedInterface $searchParams)
 * @method \App\Models\Account create(Dto $dto, array $relations = [])
 * @method \App\Models\Account find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\Account findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Account findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    /**
     * @throws \Exception
     */
    public function getStudentAccount(Student $student): Account
    {
        return $this->getService()->getStudentAccount($student);
    }

    /**
     * @throws \Exception
     */
    public function getInstructorAccount(Instructor $instructor): Account
    {
        return $this->getService()->getInstructorAccount($instructor);
    }

    /**
     * @throws \Exception
     */
    public function getOperationalAccount(Branch $branch): Account
    {
        return $this->getService()->getOperationalAccount($branch);
    }

    /**
     * @throws \Exception
     */
    public function getSavingsAccount(Branch $branch): Account
    {
        return $this->getService()->getSavingsAccount($branch);
    }

    /**
     * @param Account $account
     * @param int $amount
     * @throws Exceptions\InsufficientFundsAccountException
     */
    public function checkFunds(Account $account, int $amount): void
    {
        $this->getService()->checkFunds($account, $amount);
    }

    public function getAmount(Account $account): int
    {
        return $this->getService()->getAmount($account);
    }

    public function getBonusAmount(Account $account): int
    {
        return $this->getService()->getBonusAmount($account);
    }

    public function getTotalAmount(Account $account): int
    {
        return $this->getService()->getTotalAmount($account);
    }
}