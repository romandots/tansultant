<?php
/**
 * File: AccountRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Instructor;
use App\Models\Student;

/**
 * Class AccountRepository
 * @package App\Repository
 */
class AccountRepository
{
    /**
     * @param int $ownerId
     * @return Account
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findInstructorPersonalAccountByOwnerId(int $ownerId): Account
    {
        return Account::query()
            ->where('type', Account::TYPE_PERSONAL)
            ->where('owner_type', Instructor::class)
            ->where('owner_id', $ownerId)
            ->firstOrFail();
    }

    /**
     * @param int $ownerId
     * @return Account
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findStudentPersonalAccountByOwnerId(int $ownerId): Account
    {
        return Account::query()
            ->where('type', Account::TYPE_PERSONAL)
            ->where('owner_type', Student::class)
            ->where('owner_id', $ownerId)
            ->firstOrFail();
    }

    /**
     * @param $ownerId
     * @return Account
     */
    public function findBranchSavingsAccountByOwnerId($ownerId): Account
    {
        return Account::query()
            ->where('type', Account::TYPE_SAVINGS)
            ->where('owner_type', Branch::class)
            ->where('owner_id', $ownerId)
            ->firstOrFail();
    }

    /**
     * @param $ownerId
     * @return Account
     */
    public function findBranchOperationalAccountByOwnerId($ownerId): Account
    {
        return Account::query()
            ->where('type', Account::TYPE_OPERATIONAL)
            ->where('owner_type', Branch::class)
            ->where('owner_id', $ownerId)
            ->firstOrFail();
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $ownerType
     * @param int $ownerId
     * @return Account
     * @throws \Exception
     */
    private function create(string $name, string $type, string $ownerType, int $ownerId): Account
    {
        $account = new Account;
        $account->id = \uuid();
        $account->name = $name;
        $account->type = $type;
        $account->owner_id = $ownerId;
        $account->owner_type = $ownerType;
        $account->save();

        return $account;
    }

    /**
     * @param Student $student
     * @return Account
     * @throws \Exception
     */
    public function createStudentPersonalAccount(Student $student): Account
    {
        $name = \trans('account.name_presets.student', ['student' => $student->name]);
        $type = Account::TYPE_PERSONAL;
        $ownerId = $student->id;
        $ownerType = Student::class;

        return $this->create($name, $type, $ownerType, $ownerId);
    }

    /**
     * @param Instructor $instructor
     * @return Account
     * @throws \Exception
     */
    public function createInstructorPersonalAccount(Instructor $instructor): Account
    {
        $name = \trans('account.name_presets.instructor', ['instructor' => $instructor->name]);
        $type = Account::TYPE_PERSONAL;
        $ownerId = $instructor->id;
        $ownerType = Instructor::class;

        return $this->create($name, $type, $ownerType, $ownerId);
    }

    /**
     * @param object $branch
     * @return Account
     * @throws \Exception
     */
    public function createBranchSavingsAccount($branch): Account
    {
        $name = \trans('account.name_presets.branch_savings', ['branch' => $branch->name]);
        $type = Account::TYPE_SAVINGS;
        $ownerId = $branch->id;
        $ownerType = Branch::class;

        return $this->create($name, $type, $ownerType, $ownerId);
    }

    /**
     * @param object $branch
     * @return Account
     * @throws \Exception
     */
    public function createBranchOperationalAccount($branch): Account
    {
        $name = \trans('account.name_presets.branch_operational', ['branch' => $branch->name]);
        $type = Account::TYPE_OPERATIONAL;
        $ownerId = $branch->id;
        $ownerType = Branch::class;

        return $this->create($name, $type, $ownerType, $ownerId);
    }
}
