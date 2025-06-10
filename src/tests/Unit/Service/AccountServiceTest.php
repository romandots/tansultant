<?php
/**
 * File: AccountServiceTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Unit\Service;

use App\Components\Loader;
use App\Models\Account;
use App\Models\Branch;
use App\Models\Enum\AccountOwnerType;
use App\Models\Enum\AccountType;
use App\Models\Enum\BonusStatus;
use App\Models\Enum\TransactionStatus;
use App\Models\Instructor;
use App\Models\Student;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class AccountServiceTest
 * @package Tests\Unit
 */
class AccountServiceTest extends TestCase
{
    protected \App\Components\Account\Facade $facade;
    protected \App\Components\Account\Service $service;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->facade = Loader::accounts();
        $this->service = $this->facade->getService();
        $this->branch = Branch::factory()->create();
    }

    /**
     * @throws \Exception
     */
    public function testGetOperationalAccount(): void
    {
        $this->assertDatabaseMissing(Account::TABLE, [
            'owner_id' => $this->branch->id,
            'owner_type' => AccountOwnerType::fromClass(Branch::class),
            'type' => AccountType::OPERATIONAL->value
        ]);

        $account = $this->service->getOperationalAccount($this->branch);
        $name = \trans('account.name_presets.branch_operational',
            ['branch' => $this->branch->name]);

        $this->assertDatabaseHas(Account::TABLE, [
            'id' => $account->id,
            'name' => $name,
            'owner_id' => $this->branch->id,
            'owner_type' => AccountOwnerType::fromClass(Branch::class),
            'type' => AccountType::OPERATIONAL->value
        ]);

        $nextAccount = $this->service->getOperationalAccount($this->branch);

        $this->assertEquals($account->id, $nextAccount->id);
    }

    /**
     * @throws \Exception
     */
    public function testGetSavingsAccount(): void
    {
        $this->assertDatabaseMissing(Account::TABLE, [
            'owner_id' => $this->branch->id,
            'owner_type' => AccountOwnerType::fromClass(Branch::class),
            'type' => AccountType::SAVINGS->value
        ]);

        $account = $this->service->getSavingsAccount($this->branch);
        $name = \trans('account.name_presets.branch_savings',
            ['branch' => $this->branch->name]);

        $this->assertDatabaseHas(Account::TABLE, [
            'id' => $account->id,
            'name' => $name,
            'owner_id' => $this->branch->id,
            'owner_type' => AccountOwnerType::fromClass(Branch::class),
            'type' => AccountType::SAVINGS->value
        ]);

        $nextAccount = $this->service->getSavingsAccount($this->branch);

        $this->assertEquals($account->id, $nextAccount->id);
    }

    /**
     * @throws \Exception
     */
    public function testGetInstructorAccount(): void
    {
        $instructor = $this->createFakeInstructor();

        $this->assertDatabaseMissing(Account::TABLE, [
            'owner_id' => $instructor->id,
            'owner_type' => AccountOwnerType::fromClass(Instructor::class),
            'type' => AccountType::PERSONAL->value
        ]);

        $account = $this->service->getInstructorAccount($instructor);
        $name = \trans('account.name_presets.instructor',
            ['instructor' => $instructor->name]);

        $this->assertDatabaseHas(Account::TABLE, [
            'id' => $account->id,
            'name' => $name,
            'owner_id' => $instructor->id,
            'owner_type' => AccountOwnerType::fromClass(Instructor::class),
            'type' => AccountType::PERSONAL->value
        ]);

        $nextAccount = $this->service->getInstructorAccount($instructor);

        $this->assertEquals($account->id, $nextAccount->id);
    }

    /**
     * @throws \Exception
     */
    public function testGetStudentAccount(): void
    {
        $student = $this->createFakeStudent();

        $this->assertDatabaseMissing(Account::TABLE, [
            'owner_id' => $student->id,
            'owner_type' => AccountOwnerType::fromClass(Student::class),
            'type' => AccountType::PERSONAL->value
        ]);

        $account = $this->service->getStudentAccount($student);
        $name = \trans('account.name_presets.student',
            ['student' => $student->name]);

        $this->assertDatabaseHas(Account::TABLE, [
            'id' => $account->id,
            'name' => $name,
            'owner_id' => $student->id,
            'owner_type' => AccountOwnerType::fromClass(Student::class),
            'type' => AccountType::PERSONAL->value
        ]);

        $nextAccount = $this->service->getStudentAccount($student);

        $this->assertEquals($account->id, $nextAccount->id);
    }

    public function testGetAmount(): void
    {
        $user = $this->createFakeUser();
        $account = $this->createFakeAccount();
        $anotherAccount = $this->createFakeAccount();
        $this->generatePayments($user, $account, $anotherAccount);

        $amount = $this->service->getAmount($account);

        $this->assertEquals(100, $amount);
    }

    public function testGetBonusAmount(): void
    {
        $user = $this->createFakeUser();
        $account = $this->createFakeAccount();
        $anotherAccount = $this->createFakeAccount();
        $this->generatePayments($user, $account, $anotherAccount);

        $amount = $this->service->getBonusAmount($account);

        $this->assertEquals(100, $amount);
    }

    public function testGetTotalAmount(): void
    {
        $user = $this->createFakeUser();
        $account = $this->createFakeAccount();
        $anotherAccount = $this->createFakeAccount();
        $this->generatePayments($user, $account, $anotherAccount);

        $amount = $this->service->getTotalAmount($account);

        $this->assertEquals(200, $amount);
    }

    public function testCheckFunds(): void
    {
        $account = $this->createFakeAccountWithBalance(200);

        $this->expectException(\App\Components\Account\Exceptions\InsufficientFundsAccountException::class);
        $this->service->checkFunds($account, 300);

        $this->service->checkFunds($account, 200);
    }

    protected function generatePayments(\App\Models\User $user, Account $account, Account $anotherAccount): void
    {
        $userId = ['user_id' => $user->id];
        $deleted = ['deleted_at' => Carbon::now()];
        $statusConfirmed = ['status' => TransactionStatus::CONFIRMED];
        $statusPending = ['status' => TransactionStatus::PENDING];
        $statusCanceled = ['status' => TransactionStatus::CANCELED];
        $statusExpired = ['status' => TransactionStatus::EXPIRED];
        $bonusActivated = ['status' => BonusStatus::ACTIVATED];
        $bonusPending = ['status' => BonusStatus::PENDING];
        $bonusCanceled = ['status' => BonusStatus::CANCELED];
        $bonusExpired = ['status' => BonusStatus::EXPIRED];

        $this->createFakeTransaction(100, $account, $userId + $statusConfirmed);
        $this->createFakeTransaction(1, $account, $userId + $statusConfirmed + $deleted);
        $this->createFakeTransaction(1, $account, $userId + $statusPending);
        $this->createFakeTransaction(1, $account, $userId + $statusCanceled);
        $this->createFakeTransaction(1, $account, $userId + $statusExpired);
        $this->createFakeTransaction(1, $anotherAccount, $userId + $statusConfirmed);

        $this->createFakeBonus(100, $account, $userId + $bonusPending);
        $this->createFakeBonus(1, $account, $userId + $bonusActivated);
        $this->createFakeBonus(1, $account, $userId + $bonusCanceled);
        $this->createFakeBonus(1, $account, $userId + $bonusExpired);
        $this->createFakeBonus(1, $anotherAccount, $userId + $bonusActivated);
    }
}
