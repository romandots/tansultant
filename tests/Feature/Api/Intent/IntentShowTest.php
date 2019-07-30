<?php
/**
 * File: IntentShowTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Lesson;

use App\Models\Intent;
use App\Services\Permissions\IntentsPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakeCourse;
use Tests\Traits\CreatesFakeInstructor;
use Tests\Traits\CreatesFakeIntent;
use Tests\Traits\CreatesFakeLesson;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeSchedule;
use Tests\Traits\CreatesFakeStudent;
use Tests\Traits\CreatesFakeUser;

/**
 * Class LessonShowTest
 * @package Tests\Feature\Api\Lesson
 */
class IntentShowTest extends TestCase
{
    use CreatesFakeUser, CreatesFakeLesson, CreatesFakeSchedule, CreatesFakeInstructor, CreatesFakeCourse,
        CreatesFakePerson, CreatesFakeIntent, CreatesFakeStudent;

    protected const URL = '/intents';

    private const JSON_STRUCTURE = [
        'data' => [
            'id',
            'student',
            'manager',
            'lesson',
            'event_type',
            'created_at',
        ]
    ];

    /**
     * @var Intent
     */
    private $intent;

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
        $lesson = $this->createFakeLesson([
            'course_id' => $course->id,
            'instructor_id' => $instructor->id,
            'schedule_id' => $schedule->id,
            'controller_id' => null
        ]);
        $student = $this->createFakeStudent();
        $this->intent = $this->createFakeLessonIntent($lesson, $student, null, ['manager_id' => null]);
        $this->url = self::URL . '/' . $this->intent->id;
    }

    public function testAccessDenied(): void
    {
        $this
            ->get($this->url)
            ->assertStatus(401);
    }

    public function testNoPermission(): void
    {
        $user = $this->createFakeUser();

        $this
            ->actingAs($user, 'api')
            ->get($this->url)
            ->assertStatus(403);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser([], [
            IntentsPermissions::READ_INTENTS
        ]);

        $this
            ->actingAs($user, 'api')
            ->get($this->url)
            ->assertOk()
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'id' => $this->intent->id,
                    'event_type' => 'Lesson',
                    'created_at' => $this->intent->created_at->toDateTimeString(),
                ]
            ]);
    }
}
