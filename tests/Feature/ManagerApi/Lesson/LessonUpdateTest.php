<?php
/**
 * File: LessonUpdateTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Lesson;

use App\Models\Lesson;
use App\Services\Permissions\LessonsPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class LessonUpdateTest
 * @package Tests\Feature\Api\Lesson
 */
class LessonUpdateTest extends TestCase
{
    use CreatesFakes;

    protected const URL = 'manager_api/v1/lessons';

    private const JSON_STRUCTURE = [
        'data' => [
            'id',
            'name',
            'status',
            'status_label',
            'type',
            'type_label',
            'instructor',
            'course',
            'controller',
            'starts_at',
            'ends_at',
            'closed_at',
            'canceled_at',
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

    /**
     * @var \App\Models\Course
     */
    private $course;

    public function setUp(): void
    {
        parent::setUp();
        $instructor = $this->createFakeInstructor();
        $this->course = $this->createFakeCourse(['instructor_id' => $instructor->id]);
        $this->lesson = $this->createFakeLesson([
            'course_id' => $this->course->id,
            'instructor_id' => $instructor->id,
            'schedule_id' => null,
            'controller_id' => null,
            'status' => Lesson::STATUS_BOOKED,
        ]);
        $this->url = self::URL . '/' . $this->lesson->id;
    }

    public function testAccessDenied(): void
    {
        $this
            ->put($this->url)
            ->assertStatus(401);
    }

    public function testNoPermission(): void
    {
        $user = $this->createFakeUser();

        $this
            ->actingAs($user, 'api')
            ->put($this->url)
            ->assertStatus(403);
    }

    /**
     * @param array $data
     * @dataProvider provideInvalidData
     */
    public function testValidationErrors(array $data): void
    {
        $user = $this->createFakeManagerUser([], [
            LessonsPermissions::UPDATE_LESSONS
        ]);

        $this
            ->actingAs($user, 'api')
            ->put($this->url, $data)
            ->assertStatus(422);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser([], [
            LessonsPermissions::UPDATE_LESSONS
        ]);

        $startsAt = \Carbon\Carbon::parse($this->faker->dateTime)->setSecond(0);
        $endsAt = $startsAt->clone()->addHour();

        $data = [
            'branch_id' => $this->createFakeBranch()->id,
            'classroom_id' => $this->createFakeClassroom()->id,
            'course_id' => $this->course->id,
            'starts_at' => $startsAt->format('Y-m-d H:i'),
            'ends_at' => $endsAt->format('Y-m-d H:i'),
            'type' => Lesson::TYPE_LESSON
        ];

        $this
            ->actingAs($user, 'api')
            ->put($this->url, $data)
            ->assertOk()
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'status' => Lesson::STATUS_BOOKED,
                    'status_label' => \trans('lesson.' . Lesson::STATUS_BOOKED),
                    'type' => Lesson::TYPE_LESSON,
                    'type_label' => \trans('lesson.' . Lesson::TYPE_LESSON),
                    'starts_at' => $startsAt->toDateTimeString(),
                    'ends_at' => $endsAt->toDateTimeString(),
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
                    'branch_id' => 1,
                    'classroom_id' => 1,
                    'course_id' => 1,
                    'starts_at' => '2019-09-09 12:00:00',
                    'ends_at' => '2019-09-09 13:00:00',
                ]
            ],
            [
                [
                    'branch_id' => 1,
                    'classroom_id' => 1,
                    'course_id' => 1,
                    'starts_at' => '2019-09-09 12:00:00',
                    'type' => Lesson::TYPE_LESSON
                ]
            ],
            [
                [
                    'branch_id' => 1,
                    'classroom_id' => 1,
                    'course_id' => 1,
                    'ends_at' => '2019-09-09 13:00:00',
                    'type' => Lesson::TYPE_LESSON
                ]
            ],
            [
                [
                    'branch_id' => 'text',
                    'classroom_id' => 1,
                    'course_id' => 1,
                    'starts_at' => '2019-09-09 12:00:00',
                    'ends_at' => '2019-09-09 13:00:00',
                    'type' => Lesson::TYPE_LESSON
                ]
            ],
            [
                [
                    'branch_id' => 1,
                    'classroom_id' => 'text',
                    'course_id' => 1,
                    'starts_at' => '2019-09-09 12:00:00',
                    'ends_at' => '2019-09-09 13:00:00',
                    'type' => Lesson::TYPE_LESSON
                ]
            ],
            [
                [
                    'branch_id' => 1,
                    'classroom_id' => 1,
                    'course_id' => 'text',
                    'starts_at' => '2019-09-09 12:00:00',
                    'ends_at' => '2019-09-09 13:00:00',
                    'type' => Lesson::TYPE_LESSON
                ]
            ],
        ];
    }
}
