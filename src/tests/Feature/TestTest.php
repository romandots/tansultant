<?php

namespace Tests\Feature;

use Tests\TestCase;

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
        $this->createFakeTransaction();
        $this->createFakeSchedule();
        $this->createFakeInternalTransaction();
        $this->createFakeUser();
    }

    public function testTest(): void
    {
        $this->assertTrue(true);
    }
}