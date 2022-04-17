<?php
/**
 * File: IntentStoreTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Lesson;

use App\Models\Lesson;
use App\Services\Permissions\IntentsPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class LessonStoreTest
 * @package Tests\Feature\Api\Lesson
 */
class IntentStoreTest extends TestCase
{
    use CreatesFakes;

    protected const URL = 'admin/intents';

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
        $this->url = self::URL;
    }

    public function testAccessDenied(): void
    {
        $this
            ->post($this->url)
            ->assertStatus(401);
    }

    public function testNoPermission(): void
    {
        $user = $this->createFakeUser();

        $this
            ->actingAs($user, 'api')
            ->post($this->url)
            ->assertStatus(403);
    }

    /**
     * @param array $data
     * @dataProvider provideInvalidData
     */
    public function testValidationErrors(array $data): void
    {
        $this->artisan('db:seed');
        $user = $this->createFakeManagerUser([], [
            IntentsPermissions::CREATE
        ]);

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(422);
    }

    public function testSuccess(): void
    {
        $this->artisan('db:seed');
        $user = $this->createFakeManagerUser([], [
            IntentsPermissions::CREATE
        ]);

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

        $data = [
            'student_id' => $student->id,
            'lesson_id' => $lesson->id,
        ];

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(201)
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'lesson' => [
                        'id' => $lesson->id
                    ],
                    'student' => [
                        'id' => $student->id
                    ],
                    'event_type' => 'Lesson',
                ]
            ]);
    }

    /**
     * @return array
     */
    public function provideInvalidData(): array
    {
        return [
            [
                [
                    'student_id' => 'text value',
                    'lesson_id' => 1,
                ]
            ],
            [
                [
                    'student_id' => 1,
                    'lesson_id' => 'text value',
                ]
            ],
            [
                [
                    'lesson_id' => 1,
                ]
            ],
            [
                [
                    'student_id' => 1,
                ]
            ],
        ];
    }
}
