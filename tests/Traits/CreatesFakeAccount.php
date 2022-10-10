<?php
/**
 * File: CreatesFakeAccount.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Enum\AccountType;
use App\Models\Enum\TransactionStatus;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;

/**
 * Trait CreatesFakeAccount
 * @package Tests\Unit
 */
trait CreatesFakeAccount
{
    /**
     * @param array|null $attributes
     * @return Account
     */
    protected function createFakeAccount(?array $attributes = []): Account
    {
        return Account::factory()->create($attributes);
    }

    /**
     * @param int $amount
     * @param User|null $user
     * @param array|null $attributes
     * @return Account
     */
    protected function createFakeAccountWithBalance(int $amount, ?User $user = null, ?array $attributes = []): Account
    {
        $account = $this->createFakeAccount($attributes);
        $user = $user ?: $this->createFakeUser();
        $this->createFakeTransaction($amount, $account, [
            'status' => TransactionStatus::CONFIRMED->value,
            'confirmed_at' => Carbon::now(),
            'user_id' => $user->id
        ]);

        return $account;
    }

    /**
     * @param Student $student
     * @param array|null $attributes
     * @return Account
     */
    protected function createFakeStudentAccount(Student $student, ?array $attributes = []): Account
    {
        $attributes['owner_type'] = Student::class;
        $attributes['owner_id'] = $student->id;
        $attributes['type'] = AccountType::PERSONAL;

        return $this->createFakeAccount($attributes);
    }

    /**
     * @param Instructor $instructor
     * @param array|null $attributes
     * @return Account
     */
    protected function createFakeInstructorAccount(Instructor $instructor, ?array $attributes = []): Account
    {
        $attributes['owner_type'] = Instructor::class;
        $attributes['owner_id'] = $instructor->id;
        $attributes['type'] = AccountType::PERSONAL;

        return $this->createFakeAccount($attributes);
    }

    /**
     * @param Branch $branch
     * @param array|null $attributes
     * @return Account
     */
    protected function createFakeSavingsAccount(Branch $branch, ?array $attributes = []): Account
    {
        $attributes['owner_type'] = Branch::class;
        $attributes['owner_id'] = $branch->id;
        $attributes['type'] = AccountType::SAVINGS;

        return $this->createFakeAccount($attributes);
    }

    /**
     * @param Branch $branch
     * @param array|null $attributes
     * @return Account
     */
    protected function createFakeOperationalAccount(Branch $branch, ?array $attributes = []): Account
    {
        $attributes['owner_type'] = Branch::class;
        $attributes['owner_id'] = $branch->id;
        $attributes['type'] = AccountType::OPERATIONAL;

        return $this->createFakeAccount($attributes);
    }
}
