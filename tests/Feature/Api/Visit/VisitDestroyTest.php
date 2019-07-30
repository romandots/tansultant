<?php
/**
 * File: VisitDestroyTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Lesson;

use App\Models\Payment;
use App\Models\Visit;
use App\Services\Permissions\VisitsPermissions;
use Carbon\Carbon;
use Tests\TestCase;
use Tests\Traits\CreatesFakeAccount;
use Tests\Traits\CreatesFakeBranch;
use Tests\Traits\CreatesFakeCourse;
use Tests\Traits\CreatesFakeInstructor;
use Tests\Traits\CreatesFakeLesson;
use Tests\Traits\CreatesFakePayment;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeSchedule;
use Tests\Traits\CreatesFakeStudent;
use Tests\Traits\CreatesFakeUser;
use Tests\Traits\CreatesFakeVisit;

/**
 * Class LessonDestroyTest
 * @package Tests\Feature\Api\Lesson
 */
class VisitDestroyTest extends TestCase
{
    use CreatesFakeUser, CreatesFakeLesson, CreatesFakeSchedule, CreatesFakeInstructor, CreatesFakeCourse,
        CreatesFakePerson, CreatesFakeVisit, CreatesFakeStudent, CreatesFakePayment, CreatesFakeAccount, CreatesFakeBranch;

    protected const URL = '/visits';

    /**
     * @var Visit
     */
    private $visit;

    /**
     * @var string
     */
    private $url;

    /**
     * @var Payment
     */
    private $payment;

    /**
     * @var \App\Models\Account
     */
    private $studentAccount;

    /**
     * @var \App\Models\Account
     */
    private $savingsAccount;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $instructor = $this->createFakeInstructor();
        $course = $this->createFakeCourse(['instructor_id' => $instructor->id]);
        $schedule = $this->createFakeSchedule(['course_id' => $course->id]);
        $lesson = $this->createFakeLesson([
            'course_id' => $course->id,
            'instructor_id' => $instructor->id,
            'schedule_id' => $schedule->id,
            'controller_id' => null
        ]);

        $student = $this->createFakeStudent();
        $branch = $this->createFakeBranch();
        $this->studentAccount = $this->createFakeStudentAccount($student);
        $this->savingsAccount = $this->createFakeSavingsAccount($branch);

        $this->visit = $this->createFakeLessonVisit($lesson, $student, null, ['manager_id' => null]);
        $transaction = $this->createFakeTransaction(100, $this->studentAccount, $this->savingsAccount, [
            'status' => Payment::STATUS_CONFIRMED,
            'confirmed_at' => Carbon::now(),
            'object_id' => $this->visit->id,
            'object_type' => Visit::class
        ]);
        $this->payment = $transaction[1];
        $this->visit->payment_id = $this->payment->id;
        $this->visit->save();

        $this->url = self::URL . '/' . $this->visit->id;
    }

    public function testAccessDenied(): void
    {
        $this
            ->delete($this->url)
            ->assertStatus(401);
    }

    public function testNoPermission(): void
    {
        $user = $this->createFakeUser();

        $this
            ->actingAs($user, 'api')
            ->delete($this->url)
            ->assertStatus(403);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeUser([], [
            VisitsPermissions::DELETE_VISITS
        ]);

        $this->assertDatabaseHas(Visit::TABLE, [
            'id' => $this->visit->id,
            'payment_type' => Payment::class
        ]);
        $this->assertDatabaseMissing(Visit::TABLE, [
            'id' => $this->visit->id,
            'payment_id' => null
        ]);
        $this->assertDatabaseHas(Payment::TABLE, [
            'object_id' => $this->visit->id,
            'object_type' => Visit::class,
            'deleted_at' => null,
            'account_id' => $this->savingsAccount->id,
            'amount' => 100
        ]);
        $this->assertDatabaseHas(Payment::TABLE, [
            'object_id' => $this->visit->id,
            'object_type' => Visit::class,
            'deleted_at' => null,
            'account_id' => $this->studentAccount->id,
            'amount' => -100
        ]);

        $this
            ->actingAs($user, 'api')
            ->delete($this->url)
            ->assertOk();

        $this->assertDatabaseMissing(Visit::TABLE, ['id' => $this->visit->id]);
        $this->assertDatabaseMissing(Payment::TABLE, [
            'object_id' => $this->visit->id,
            'object_type' => Visit::class,
            'deleted_at' => null,
            'account_id' => $this->savingsAccount->id,
            'amount' => 100
        ]);
        $this->assertDatabaseMissing(Payment::TABLE, [
            'object_id' => $this->visit->id,
            'object_type' => Visit::class,
            'deleted_at' => null,
            'account_id' => $this->studentAccount->id,
            'amount' => -100
        ]);
    }
}
