<?php
/**
 * File: VisitStoreTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Lesson;

use App\Models\Account;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Visit;
use App\Services\Permissions\VisitsPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class LessonStoreTest
 * @package Tests\Feature\Api\Lesson
 */
class VisitStoreTest extends TestCase
{
    use CreatesFakes;

    protected const URL = 'admin/visits';

    private const JSON_STRUCTURE = [
        'data' => [
            'id',
            'student',
            'manager',
            'lesson',
            'event_type',
            'payment_type',
            'created_at',
        ]
    ];

    /**
     * @var Visit
     */
    private $visit;

    /**
     * @var string
     */
    private $url;

    /**
     * @var \App\Models\Lesson
     */
    private $lesson;

    /**
     * @var Student
     */
    private $student;

    public function setUp(): void
    {
        parent::setUp();
        $this->url = self::URL;
        $instructor = $this->createFakeInstructor();
        $course = $this->createFakeCourse(['instructor_id' => $instructor->id]);
        $schedule = $this->createFakeSchedule(['course_id' => $course->id]);
        $this->student = $this->createFakeStudent();
        $branch = $this->createFakeBranch();
        $this->createFakeAccountWithBalance(100, null, [
            'type' => Account::TYPE_PERSONAL,
            'owner_type' => Student::class,
            'owner_id' => $this->student->id
        ]);
        $this->lesson = $this->createFakeLesson([
            'course_id' => $course->id,
            'instructor_id' => $instructor->id,
            'schedule_id' => $schedule->id,
            'controller_id' => null,
            'branch_id' => $branch
        ]);
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
        $user = $this->createFakeManagerUser([], [
            VisitsPermissions::CREATE
        ]);

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(422);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser([], [
            VisitsPermissions::CREATE
        ]);

        $data = [
            'student_id' => $this->student->id,
            'lesson_id' => $this->lesson->id
        ];

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(201)
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'lesson' => [
                        'id' => $this->lesson->id
                    ],
                    'student' => [
                        'id' => $this->student->id
                    ],
                    'event_type' => 'Lesson',
                ]
            ]);

        $this->assertDatabaseHas(Visit::TABLE, [
            'student_id' => $this->student->id,
            'event_id' => $this->lesson->id,
            'event_type' => Lesson::class
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
                    'lesson_id' => 1
                ]
            ],
            [
                [
                    'student_id' => 1,
                    'lesson_id' => 'text value'
                ]
            ],
            [
                [
                    'lesson_id' => 1
                ]
            ],
            [
                [
                    'student_id' => 1
                ]
            ],
            [
                [
                    'lesson_id' => 1,
                    'student_id' => 1,
                    'promocode_id' => 'WrongType'
                ]
            ]
        ];
    }
}
