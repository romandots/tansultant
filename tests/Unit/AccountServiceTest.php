<?php
/**
 * File: AccountServiceTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Bonus;
use App\Models\Branch;
use App\Models\Instructor;
use App\Models\Payment;
use App\Models\Student;
use App\Services\Account\AccountService;
use App\Services\Account\Exceptions\InsufficientFundsAccountServiceException;
use Tests\TestCase;
use Tests\Traits\CreatesFakeAccount;
use Tests\Traits\CreatesFakeInstructor;
use Tests\Traits\CreatesFakePayment;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeStudent;
use Tests\Traits\CreatesFakeUser;

/**
 * Class AccountServiceTest
 * @package Tests\Unit
 */
class AccountServiceTest extends TestCase
{
    use CreatesFakeUser, CreatesFakeStudent, CreatesFakePerson, CreatesFakeInstructor, CreatesFakeAccount,
        CreatesFakePayment, AssertExceptionTrait;

    /**
     * @var AccountService
     */
    private $service;

    /**
     * @var Branch
     */
    private $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->get(AccountService::class);
        $this->branch = \factory(Branch::class)->create();
    }

    /**
     * @throws \Exception
     */
    public function testGetOperationalAccount(): void
    {
        $this->assertDatabaseMissing(Account::TABLE, [
            'owner_id' => $this->branch->id,
            'owner_type' => Branch::class,
            'type' => Account::TYPE_OPERATIONAL
        ]);

        $account = $this->service->getOperationalAccount($this->branch);
        $name = \trans('account.name_presets.branch_operational',
            ['branch' => $this->branch->name]);

        $this->assertDatabaseHas(Account::TABLE, [
            'id' => $account->id,
            'name' => $name,
            'owner_id' => $this->branch->id,
            'owner_type' => Branch::class,
            'type' => Account::TYPE_OPERATIONAL
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
            'owner_type' => Branch::class,
            'type' => Account::TYPE_SAVINGS
        ]);

        $account = $this->service->getSavingsAccount($this->branch);
        $name = \trans('account.name_presets.branch_savings',
            ['branch' => $this->branch->name]);

        $this->assertDatabaseHas(Account::TABLE, [
            'id' => $account->id,
            'name' => $name,
            'owner_id' => $this->branch->id,
            'owner_type' => Branch::class,
            'type' => Account::TYPE_SAVINGS
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
            'owner_type' => Instructor::class,
            'type' => Account::TYPE_PERSONAL
        ]);

        $account = $this->service->getInstructorAccount($instructor);
        $name = \trans('account.name_presets.instructor',
            ['instructor' => $instructor->name]);

        $this->assertDatabaseHas(Account::TABLE, [
            'id' => $account->id,
            'name' => $name,
            'owner_id' => $instructor->id,
            'owner_type' => Instructor::class,
            'type' => Account::TYPE_PERSONAL
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
            'owner_type' => Student::class,
            'type' => Account::TYPE_PERSONAL
        ]);

        $account = $this->service->getStudentAccount($student);
        $name = \trans('account.name_presets.student',
            ['student' => $student->name]);

        $this->assertDatabaseHas(Account::TABLE, [
            'id' => $account->id,
            'name' => $name,
            'owner_id' => $student->id,
            'owner_type' => Student::class,
            'type' => Account::TYPE_PERSONAL
        ]);

        $nextAccount = $this->service->getStudentAccount($student);

        $this->assertEquals($account->id, $nextAccount->id);
    }

    public function testGetAmount(): void
    {
        $user = $this->createFakeUser();
        $account = $this->createFakeAccount();
        $anotherAccount = $this->createFakeAccount();
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_CONFIRMED
        ]);
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_PENDING
        ]);
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_CANCELED
        ]);
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_EXPIRED
        ]);
        $this->createFakePayment(100, $anotherAccount, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_CONFIRMED
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_ACTIVATED
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_PENDING
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_CANCELED
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_EXPIRED
        ]);
        $this->createFakeBonus(100, $anotherAccount, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_ACTIVATED
        ]);

        $amount = $this->service->getAmount($account);

        $this->assertEquals(100, $amount);
    }

    public function testGetBonusAmount(): void
    {
        $user = $this->createFakeUser();
        $account = $this->createFakeAccount();
        $anotherAccount = $this->createFakeAccount();
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_CONFIRMED
        ]);
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_PENDING
        ]);
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_CANCELED
        ]);
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_EXPIRED
        ]);
        $this->createFakePayment(100, $anotherAccount, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_CONFIRMED
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_ACTIVATED
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_PENDING
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_CANCELED
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_EXPIRED
        ]);
        $this->createFakeBonus(100, $anotherAccount, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_PENDING
        ]);

        $amount = $this->service->getBonusAmount($account);

        $this->assertEquals(100, $amount);
    }

    public function testGetTotalAmount(): void
    {
        $user = $this->createFakeUser();
        $account = $this->createFakeAccount();
        $anotherAccount = $this->createFakeAccount();
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_CONFIRMED
        ]);
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_PENDING
        ]);
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_CANCELED
        ]);
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_EXPIRED
        ]);
        $this->createFakePayment(100, $anotherAccount, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_CONFIRMED
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_ACTIVATED
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_PENDING
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_CANCELED
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_EXPIRED
        ]);
        $this->createFakeBonus(100, $anotherAccount, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_ACTIVATED
        ]);

        $amount = $this->service->getTotalAmount($account);

        $this->assertEquals(200, $amount);
    }

    public function testCheckFunds(): void
    {
        $user = $this->createFakeUser();
        $account = $this->createFakeAccount();
        $anotherAccount = $this->createFakeAccount();
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_CONFIRMED
        ]);
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_PENDING
        ]);
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_CANCELED
        ]);
        $this->createFakePayment(100, $account, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_EXPIRED
        ]);
        $this->createFakePayment(100, $anotherAccount, [
            'user_id' => $user->id,
            'status' => Payment::STATUS_CONFIRMED
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_ACTIVATED
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_PENDING
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_CANCELED
        ]);
        $this->createFakeBonus(100, $account, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_EXPIRED
        ]);
        $this->createFakeBonus(100, $anotherAccount, [
            'user_id' => $user->id,
            'status' => Bonus::STATUS_ACTIVATED
        ]);

        $this->assertException(function () use ($account) {
            $this->service->checkFunds($account, 300);
        }, InsufficientFundsAccountServiceException::class);

        $this->service->checkFunds($account, 200);
    }
}
