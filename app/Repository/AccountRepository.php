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
            ->where('owner', Instructor::class)
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
            ->where('owner', Student::class)
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
            ->where('owner', 'App\Models\Branch')
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
            ->where('owner', 'App\Models\Branch')
            ->where('owner_id', $ownerId)
            ->firstOrFail();
    }

    /**
     * @param Student $student
     * @return Account
     */
    public function createStudentPersonalAccount(Student $student): Account
    {
        $account = new Account;
        $account->name = \trans('account.name_presets.student', ['student' => $student->name]);
        $account->type = Account::TYPE_PERSONAL;
        $account->owner_id = $student->id;
        $account->owner_type = Student::class;

        return $account;
    }

    /**
     * @param Instructor $instructor
     * @return Account
     */
    public function createInstructorPersonalAccount(Instructor $instructor): Account
    {
        $account = new Account;
        $account->name = \trans('account.name_presets.instructor', ['instructor' => $instructor->name]);
        $account->type = Account::TYPE_PERSONAL;
        $account->owner_id = $instructor->id;
        $account->owner_type = Instructor::class;

        return $account;
    }

    /**
     * @param object $branch
     * @return Account
     */
    public function createBranchSavingsAccount($branch): Account
    {
        $account = new Account;
        $account->name = \trans('account.name_presets.branch_savings', ['branch' => $branch->name]);
        $account->type = Account::TYPE_SAVINGS;
        $account->owner_id = $branch->id;
        $account->owner_type = 'App\Models\Branch';

        return $account;
    }

    /**
     * @param object $branch
     * @return Account
     */
    public function createBranchOperationalAccount($branch): Account
    {
        $account = new Account;
        $account->name = \trans('account.name_presets.branch_operational', ['branch' => $branch->name]);
        $account->type = Account::TYPE_OPERATIONAL;
        $account->owner_id = $branch->id;
        $account->owner_type = 'App\Models\Branch';

        return $account;
    }
}
