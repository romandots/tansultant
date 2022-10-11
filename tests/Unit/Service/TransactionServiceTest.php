<?php
/**
 * File: PaymentServiceTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Unit\Service;

use App\Components\Account\Exceptions\InsufficientFundsAccountException;
use App\Components\Loader;
use App\Models\Enum\AccountOwnerType;
use App\Models\Enum\AccountType;
use App\Models\Enum\PaymentObjectType;
use App\Models\Enum\TransactionStatus;
use App\Models\Enum\TransactionTransferType;
use App\Models\Enum\TransactionType;
use App\Models\Transaction;
use App\Models\Visit;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class PaymentServiceTest
 * @package Tests\Unit
 */
class TransactionServiceTest extends TestCase
{
    use CreatesFakes;

    protected \App\Components\Transaction\Service $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Loader::payments()->getService();
    }

    /**
     * @throws \Exception
     */
    public function testCreateVisitPayment(): void
    {
        $price = 100;
        $branchId = $this->createFakeBranch()->id;
        $classroomId = $this->createFakeClassroom(['branch_id' => $branchId])->id;
        $lesson = $this->createFakeLesson([
            'branch_id' => $branchId,
            'classroom_id' => $classroomId,
        ]);
        $visit = $this->createFakeLessonVisit($lesson);
        $student = $this->createFakeStudent();
        $user = $this->createFakeUser();
        $studentAccount = $this->createFakeAccount([
            'type' => AccountType::PERSONAL,
            'owner_id' => $student->id
        ]);

        $savingsAccount = $this->createFakeAccount([
            'type' => AccountType::SAVINGS,
            'owner_type' => AccountOwnerType::BRANCH,
            'owner_id' => $branchId
        ]);

        $this->expectException(InsufficientFundsAccountException::class);
        $this->service->createVisitPayment($price, $visit, $student, $user);

        $this->createFakeTransaction(100, $studentAccount, [
            'status' => TransactionStatus::CONFIRMED,
            'user_id' => $user->id
        ]);

        $visitPayment = $this->service->createVisitPayment($price, $visit, $student, $user);

        $this->assertNotNull($visitPayment);
        $this->assertEquals(100, $visitPayment->amount);
        $this->assertEquals(Visit::class, $visitPayment->object_type);
        $this->assertEquals($visit->id, $visitPayment->object_id);
        $this->assertEquals($savingsAccount->id, $visitPayment->account_id);
        $this->assertEquals(TransactionType::AUTO, $visitPayment->type);
        $this->assertEquals(TransactionTransferType::INTERNAL, $visitPayment->transfer_type);
        $this->assertEquals(TransactionStatus::CONFIRMED, $visitPayment->status);
        $this->assertNotNull($visitPayment->confirmed_at);

        $relatedPayment = $visitPayment->related_payment;
        $this->assertNotNull($relatedPayment);
        $this->assertEquals(-100, $relatedPayment->amount);
        $this->assertEquals(Visit::class, $relatedPayment->object_type);
        $this->assertEquals($visit->id, $relatedPayment->object_id);
        $this->assertEquals($studentAccount->id, $relatedPayment->account_id);
        $this->assertEquals(TransactionType::AUTO, $relatedPayment->type);
        $this->assertEquals(TransactionTransferType::INTERNAL, $relatedPayment->transfer_type);
        $this->assertEquals(TransactionStatus::CONFIRMED, $relatedPayment->status);
        $this->assertNotNull($relatedPayment->confirmed_at);
        $this->assertEquals($visitPayment->id, $relatedPayment->related_id);
    }

    /**
     * @throws \Exception
     */
    public function testCreateLessonPayment(): void
    {
        $price = 100;
        $branchId = $this->createFakeBranch()->id;
        $classroomId = $this->createFakeClassroom(['branch_id' => $branchId])->id;
        $lesson = $this->createFakeLesson([
            'branch_id' => $branchId,
            'classroom_id' => $classroomId,
        ]);
        $user = $this->createFakeUser();
        $instructor = $this->createFakeInstructor();
        $instructorAccount = $this->createFakeAccount([
            'type' => AccountType::PERSONAL,
            'owner_type' => AccountOwnerType::INSTRUCTOR,
            'owner_id' => $instructor->id
        ]);

        $savingsAccount = $this->createFakeAccount([
            'type' => AccountType::SAVINGS,
            'owner_type' => AccountOwnerType::BRANCH,
            'owner_id' => $branchId
        ]);

        $lessonPayment = $this->service->createLessonPayment($price, $lesson, $instructor, $user);

        $this->assertNotNull($lessonPayment);
        $this->assertEquals(-100, $lessonPayment->amount);
        $this->assertEquals(PaymentObjectType::LESSON, $lessonPayment->object_type);
        $this->assertEquals($lesson->id, $lessonPayment->object_id);
        $this->assertEquals($savingsAccount->id, $lessonPayment->account_id);
        $this->assertEquals(TransactionType::AUTO, $lessonPayment->type);
        $this->assertEquals(TransactionTransferType::INTERNAL, $lessonPayment->transfer_type);
        $this->assertEquals(TransactionStatus::CONFIRMED, $lessonPayment->status);
        $this->assertNotNull($lessonPayment->confirmed_at);

        $relatedPayment = $lessonPayment->related_payment;
        $this->assertNotNull($relatedPayment);
        $this->assertEquals(100, $relatedPayment->amount);
        $this->assertEquals(PaymentObjectType::LESSON, $relatedPayment->object_type);
        $this->assertEquals($lesson->id, $relatedPayment->object_id);
        $this->assertEquals($instructorAccount->id, $relatedPayment->account_id);
        $this->assertEquals(TransactionType::AUTO, $relatedPayment->type);
        $this->assertEquals(TransactionTransferType::INTERNAL, $relatedPayment->transfer_type);
        $this->assertEquals(TransactionStatus::CONFIRMED, $relatedPayment->status);
        $this->assertNotNull($relatedPayment->confirmed_at);
        $this->assertEquals($lessonPayment->id, $relatedPayment->related_id);
    }

    public function testDelete(): void
    {
        $price = 100;
        $branchId = $this->createFakeBranch()->id;
        $classroomId = $this->createFakeClassroom(['branch_id' => $branchId])->id;
        $lesson = $this->createFakeLesson([
            'branch_id' => $branchId,
            'classroom_id' => $classroomId,
        ]);
        $user = $this->createFakeUser();
        $instructor = $this->createFakeInstructor();
        $instructorAccount = $this->createFakeAccount([
            'type' => AccountType::PERSONAL,
            'owner_type' => AccountOwnerType::INSTRUCTOR,
            'owner_id' => $instructor->id
        ]);

        $savingsAccount = $this->createFakeAccount([
            'type' => AccountType::SAVINGS,
            'owner_type' => AccountOwnerType::BRANCH,
            'owner_id' => $branchId
        ]);

        $payment = $this->createFakeTransaction(100);
        $related = $this->createFakeTransaction(100);

        $payment->related_id = $related->id;
        $related->related_id = $payment->id;

        $payment->save();
        $related->save();

        $this->assertDatabaseHas(Transaction::TABLE, [
            'id' => $payment->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas(Transaction::TABLE, [
            'id' => $related->id,
            'deleted_at' => null
        ]);

        $this->service->delete($payment, $user);

        $this->assertDatabaseMissing(Transaction::TABLE, [
            'id' => $payment->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseMissing(Transaction::TABLE, [
            'id' => $related->id,
            'deleted_at' => null
        ]);
    }
}
