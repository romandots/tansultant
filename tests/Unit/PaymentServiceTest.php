<?php
/**
 * File: PaymentServiceTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Instructor;
use App\Models\Lesson;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Visit;
use App\Services\Account\Exceptions\InsufficientFundsAccountServiceException;
use App\Services\Payment\PaymentService;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class PaymentServiceTest
 * @package Tests\Unit
 */
class PaymentServiceTest extends TestCase
{
    use CreatesFakes, AssertExceptionTrait;

    /**
     * @var PaymentService
     */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->get(PaymentService::class);
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
            'type' => Account::TYPE_PERSONAL,
            'owner_type' => Student::class,
            'owner_id' => $student->id
        ]);

        $savingsAccount = $this->createFakeAccount([
            'type' => Account::TYPE_SAVINGS,
            'owner_type' => Branch::class,
            'owner_id' => $branchId
        ]);

        $this->assertException(function () use ($user, $student, $visit, $price) {
            $this->service->createVisitPayment($price, $visit, $student, $user);
        }, InsufficientFundsAccountServiceException::class);

        $this->createFakePayment(100, $studentAccount, [
            'status' => Payment::STATUS_CONFIRMED,
            'user_id' => $user->id
        ]);

        $visitPayment = $this->service->createVisitPayment($price, $visit, $student, $user);

        $this->assertNotNull($visitPayment);
        $this->assertEquals(100, $visitPayment->amount);
        $this->assertEquals(Visit::class, $visitPayment->object_type);
        $this->assertEquals($visit->id, $visitPayment->object_id);
        $this->assertEquals($savingsAccount->id, $visitPayment->account_id);
        $this->assertEquals(Payment::TYPE_AUTOMATIC, $visitPayment->type);
        $this->assertEquals(Payment::TRANSFER_TYPE_INTERNAL, $visitPayment->transfer_type);
        $this->assertEquals(Payment::STATUS_CONFIRMED, $visitPayment->status);
        $this->assertNotNull($visitPayment->confirmed_at);

        $relatedPayment = $visitPayment->related_payment;
        $this->assertNotNull($relatedPayment);
        $this->assertEquals(-100, $relatedPayment->amount);
        $this->assertEquals(Visit::class, $relatedPayment->object_type);
        $this->assertEquals($visit->id, $relatedPayment->object_id);
        $this->assertEquals($studentAccount->id, $relatedPayment->account_id);
        $this->assertEquals(Payment::TYPE_AUTOMATIC, $relatedPayment->type);
        $this->assertEquals(Payment::TRANSFER_TYPE_INTERNAL, $relatedPayment->transfer_type);
        $this->assertEquals(Payment::STATUS_CONFIRMED, $relatedPayment->status);
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
            'type' => Account::TYPE_PERSONAL,
            'owner_type' => Instructor::class,
            'owner_id' => $instructor->id
        ]);

        $savingsAccount = $this->createFakeAccount([
            'type' => Account::TYPE_SAVINGS,
            'owner_type' => Branch::class,
            'owner_id' => $branchId
        ]);

        $lessonPayment = $this->service->createLessonPayment($price, $lesson, $instructor, $user);

        $this->assertNotNull($lessonPayment);
        $this->assertEquals(-100, $lessonPayment->amount);
        $this->assertEquals(Lesson::class, $lessonPayment->object_type);
        $this->assertEquals($lesson->id, $lessonPayment->object_id);
        $this->assertEquals($savingsAccount->id, $lessonPayment->account_id);
        $this->assertEquals(Payment::TYPE_AUTOMATIC, $lessonPayment->type);
        $this->assertEquals(Payment::TRANSFER_TYPE_INTERNAL, $lessonPayment->transfer_type);
        $this->assertEquals(Payment::STATUS_CONFIRMED, $lessonPayment->status);
        $this->assertNotNull($lessonPayment->confirmed_at);

        $relatedPayment = $lessonPayment->related_payment;
        $this->assertNotNull($relatedPayment);
        $this->assertEquals(100, $relatedPayment->amount);
        $this->assertEquals(Lesson::class, $relatedPayment->object_type);
        $this->assertEquals($lesson->id, $relatedPayment->object_id);
        $this->assertEquals($instructorAccount->id, $relatedPayment->account_id);
        $this->assertEquals(Payment::TYPE_AUTOMATIC, $relatedPayment->type);
        $this->assertEquals(Payment::TRANSFER_TYPE_INTERNAL, $relatedPayment->transfer_type);
        $this->assertEquals(Payment::STATUS_CONFIRMED, $relatedPayment->status);
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
            'type' => Account::TYPE_PERSONAL,
            'owner_type' => Instructor::class,
            'owner_id' => $instructor->id
        ]);

        $savingsAccount = $this->createFakeAccount([
            'type' => Account::TYPE_SAVINGS,
            'owner_type' => Branch::class,
            'owner_id' => $branchId
        ]);

        $payment = $this->createFakePayment(100);
        $related = $this->createFakePayment(100);

        $payment->related_id = $related->id;
        $related->related_id = $payment->id;

        $payment->save();
        $related->save();

        $this->assertDatabaseHas(Payment::TABLE, [
            'id' => $payment->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas(Payment::TABLE, [
            'id' => $related->id,
            'deleted_at' => null
        ]);

        $this->service->delete($payment);

        $this->assertDatabaseMissing(Payment::TABLE, [
            'id' => $payment->id,
            'deleted_at' => null
        ]);
        $this->assertDatabaseMissing(Payment::TABLE, [
            'id' => $related->id,
            'deleted_at' => null
        ]);
    }
}
