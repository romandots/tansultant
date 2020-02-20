<?php
/**
 * File: CourseStoreTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Schedule;

use App\Models\Course;
use App\Models\Schedule;
use App\Services\Permissions\SchedulesPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class ScheduleStoreTest
 * @package Tests\Feature\Api\Schedule
 */
class ScheduleStoreTest extends TestCase
{
    use CreatesFakes;

    private const JSON_STRUCTURE = [
        'data' => [
            'id',
            'branch',
            'classroom',
            'starts_at',
            'ends_at',
            'weekday',
        ]
    ];

    /**
     * @var string
     */
    private string $url;

    /**
     * @var Course
     */
    private Course $course;

    public function setUp(): void
    {
        parent::setUp();
        parent::setUp();
        $this->course = $this->createFakeCourse();
        $this->url = 'admin/courses/' . $this->course->id . '/schedules';
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
            SchedulesPermissions::CREATE
        ]);

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(422);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser([], [
            SchedulesPermissions::CREATE
        ]);

        $data = [
            'branch_id' => $this->createFakeBranch()->id,
            'classroom_id' => $this->createFakeClassroom()->id,
            'starts_at' => $this->faker->time('H:i:00'),
            'ends_at' => $this->faker->time('H:i:00'),
            'weekday' => '1',
        ];

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(201)
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'branch' => ['id' => $data['branch_id']],
                    'classroom' => ['id' => $data['classroom_id']],
                    'starts_at' => $data['starts_at'],
                    'ends_at' => $data['ends_at'],
                    'weekday' => '1',
                ]
            ]);

        $this->assertDatabaseHas(Schedule::TABLE, [
            'course_id' => $this->course->id,
            'branch_id' => $data['branch_id'],
            'classroom_id' => $data['classroom_id'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'weekday' => '1',
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
                    'starts_at' => '11:00:00',
                    'ends_at' => '12:00:00',
                ]
            ],
            [
                [
                    'starts_at' => '11:00:00',
                    'ends_at' => '12:00:00',
                    'weekday' => 60,
                ]
            ],
            [
                [
                    'starts_at' => '11:00:00',
                    'ends_at' => '12:00:00',
                    'weekday' => '',
                ]
            ],
            [
                [
                    'starts_at' => '11:00:00',
                    'ends_at' => '12:00:00',
                    'weekday' => 'buesday',
                ]
            ],
            [
                [
                    'ends_at' => '12:00:00',
                    'weekday' => '1',
                ]
            ],
            [
                [
                    'starts_at' => '11:00:00',
                    'weekday' => '1',
                ]
            ],
        ];
    }
}
