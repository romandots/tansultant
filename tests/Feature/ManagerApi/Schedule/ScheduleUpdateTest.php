<?php
/**
 * File: CourseUpdateTest.php
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
 * Class ScheduleUpdateTest
 * @package Tests\Feature\Api\Schedule
 */
class ScheduleUpdateTest extends TestCase
{
    use CreatesFakes;

    /**
     * @var Schedule[]
     */
    private array $schedules;

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
        $this->artisan('db:seed');
        $this->course = $this->createFakeCourse();
        $this->schedules = Schedule::factory(3)->create(['course_id' => $this->course->id])->all();
        $this->url = 'admin/schedules/' . $this->schedules[0]->id;
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
            SchedulesPermissions::UPDATE
        ]);

        $this
            ->actingAs($user, 'api')
            ->put($this->url, $data)
            ->assertStatus(422);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser([], [
            SchedulesPermissions::UPDATE
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
            ->put($this->url, $data)
            ->assertStatus(200);

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
                    'classroom_id' => 1,
                    'starts_at' => '11:00:00',
                    'ends_at' => '12:00:00',
                    'weekday' => 'monday',
                ]
            ],
            [
                [
                    'branch_id' => 1,
                    'starts_at' => '11:00:00',
                    'ends_at' => '12:00:00',
                    'weekday' => 'monday',
                ]
            ],
            [
                [
                    'course_id' => 1,
                    'starts_at' => '11:00:00',
                    'ends_at' => '12:00:00',
                    'weekday' => 'monday',
                ]
            ],
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
        ];
    }
}
