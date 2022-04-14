<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\CreatesFakeAccount;
use Tests\Traits\CreatesFakeBranch;
use Tests\Traits\CreatesFakeClassroom;
use Tests\Traits\CreatesFakeContract;
use Tests\Traits\CreatesFakeCourse;
use Tests\Traits\CreatesFakeCustomer;
use Tests\Traits\CreatesFakeInstructor;
use Tests\Traits\CreatesFakeIntent;
use Tests\Traits\CreatesFakeLesson;
use Tests\Traits\CreatesFakePayment;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakes;
use Tests\Traits\CreatesFakeStudent;
use Tests\Traits\CreatesFakeVisit;

class TestTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        $this->createFakeAccount();
        $this->createFakeClassroom();
        $this->createFakeInstructor();
        $this->createFakeLessonVisit();
        $this->createFakeLesson();
        $this->createFakePerson();
        $this->createFakeStudent();
        $this->createFakeBonus();
        $this->createFakeBranch();
        $this->createFakeCourse();
        $this->createFakeIntent();
        $this->createFakeManagerUser();
        $this->createFakePayment();
        $this->createFakeSchedule();
        $this->createFakeTransaction();
        $this->createFakeUser();
    }

    public function testTest(): void
    {
        $this->assertTrue(true);
    }
}