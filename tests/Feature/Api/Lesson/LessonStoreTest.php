<?php
/**
 * File: LessonStoreTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Lesson;

use App\Models\Lesson;
use App\Services\Permissions\LessonsPermissions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\CreatesFakeCourse;
use Tests\Traits\CreatesFakeInstructor;
use Tests\Traits\CreatesFakeLesson;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeUser;

/**
 * Class LessonStoreTest
 * @package Tests\Feature\Api\Lesson
 */
class LessonStoreTest extends TestCase
{
    use CreatesFakeUser, CreatesFakeLesson, CreatesFakeInstructor, CreatesFakePerson, CreatesFakeCourse, WithFaker;

    protected const URL = '/lessons';

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
        $user = $this->createFakeManagerUser([], [
            LessonsPermissions::CREATE_LESSONS
        ]);

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(422);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser([], [
            LessonsPermissions::CREATE_LESSONS
        ]);

        $instructor = $this->createFakeInstructor();
        $course = $this->createFakeCourse(['instructor_id' => $instructor->id]);

        $startsAt = \Carbon\Carbon::parse($this->faker->dateTime)->setSecond(0);
        $endsAt = $startsAt->clone()->addHour();
        $data = [
            'branch_id' => $this->faker->randomNumber(),
            'classroom_id' => $this->faker->randomNumber(),
            'course_id' => $course->id,
            'starts_at' => $startsAt->format('Y-m-d H:i'),
            'ends_at' => $endsAt->format('Y-m-d H:i'),
            'type' => Lesson::TYPE_LESSON
        ];

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(201)
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
