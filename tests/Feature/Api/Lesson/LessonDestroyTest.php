<?php
/**
 * File: LessonDestroyTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Lesson;

use App\Models\Lesson;
use App\Services\Permissions\LessonsPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakeCourse;
use Tests\Traits\CreatesFakeInstructor;
use Tests\Traits\CreatesFakeLesson;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeSchedule;
use Tests\Traits\CreatesFakeUser;

/**
 * Class LessonDestroyTest
 * @package Tests\Feature\Api\Lesson
 */
class LessonDestroyTest extends TestCase
{
    use CreatesFakeUser, CreatesFakeLesson, CreatesFakeSchedule, CreatesFakeInstructor, CreatesFakeCourse,
        CreatesFakePerson;

    protected const URL = '/api/lessons';

    /**
     * @var Lesson
     */
    private $lesson;

    /**
     * @var string
     */
    private $url;

    public function setUp(): void
    {
        parent::setUp();
        $instructor = $this->createFakeInstructor();
        $course = $this->createFakeCourse(['instructor_id' => $instructor->id]);
        $schedule = $this->createFakeSchedule(['course_id' => $course->id]);
        $this->lesson = $this->createFakeLesson([
            'course_id' => $course->id,
            'instructor_id' => $instructor->id,
            'schedule_id' => $schedule->id,
            'controller_id' => null
        ]);
        $this->url = self::URL . '/' . $this->lesson->id;
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
            LessonsPermissions::DELETE_LESSONS
        ]);

        $this
            ->actingAs($user, 'api')
            ->delete($this->url)
            ->assertOk();

        $this->assertDatabaseMissing(Lesson::TABLE, [
            'id' => $this->lesson->id,
            'deleted_at' => null
        ]);
    }
}
