<?php
/**
 * File: CourseUpdateTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Schedule;

use App\Models\Schedule;
use App\Services\Permissions\SchedulesPermissions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\CreatesFakeCourse;
use Tests\Traits\CreatesFakeInstructor;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeSchedule;
use Tests\Traits\CreatesFakeUser;

/**
 * Class ScheduleUpdateTest
 * @package Tests\Feature\Api\Schedule
 */
class ScheduleUpdateTest extends TestCase
{
    use CreatesFakeUser, CreatesFakeSchedule, CreatesFakeInstructor, CreatesFakeCourse, CreatesFakePerson, WithFaker;

    protected const URL = '/schedules';

    private const JSON_STRUCTURE = [
        'data' => [
            'id',
            'branch_id',
            'classroom_id',
            'course',
            'starts_at',
            'ends_at',
            'duration',
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday',
        ]
    ];

    /**
     * @var Schedule
     */
    private $schedule;

    /**
     * @var string
     */
    private $url;

    public function setUp(): void
    {
        parent::setUp();
        $instructor = $this->createFakeInstructor();
        $course = $this->createFakeCourse(['instructor_id' => $instructor->id]);
        $this->schedule = $this->createFakeSchedule(['course_id' => $course->id]);
        $this->url = self::URL . '/' . $this->schedule->id;
    }

    public function testAccessDenied(): void
    {
        $this
            ->patch($this->url)
            ->assertStatus(401);
    }

    public function testNoPermission(): void
    {
        $user = $this->createFakeUser();

        $this
            ->actingAs($user, 'api')
            ->patch($this->url)
            ->assertStatus(403);
    }

    /**
     * @param array $data
     * @dataProvider provideInvalidData
     */
    public function testValidationErrors(array $data): void
    {
        $user = $this->createFakeUser([], [
            SchedulesPermissions::UPDATE_SCHEDULES
        ]);

        $this
            ->actingAs($user, 'api')
            ->patch($this->url, $data)
            ->assertStatus(422);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeUser([], [
            SchedulesPermissions::UPDATE_SCHEDULES
        ]);

        $instructor = $this->createFakeInstructor();
        $course = $this->createFakeCourse(['instructor_id' => $instructor->id]);

        $data = [
            'branch_id' => $this->faker->randomNumber(),
            'classroom_id' => $this->faker->randomNumber(),
            'course_id' => $course->id,
            'starts_at' => $this->faker->date(),
            'ends_at' => $this->faker->date(),
            'duration' => 60,
            'monday' => $this->faker->time('H:i'),
            'tuesday' => $this->faker->time('H:i'),
            'wednesday' => $this->faker->time('H:i'),
            'thursday' => $this->faker->time('H:i'),
            'friday' => $this->faker->time('H:i'),
            'saturday' => $this->faker->time('H:i'),
            'sunday' => $this->faker->time('H:i'),
        ];

        $this
            ->actingAs($user, 'api')
            ->patch($this->url, $data)
            ->assertOk()
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'branch_id' => $data['branch_id'],
                    'classroom_id' => $data['classroom_id'],
                    'starts_at' => $data['starts_at'],
                    'ends_at' => $data['ends_at'],
                    'duration' => $data['duration'],
                    'monday' => $data['monday'],
                    'tuesday' => $data['tuesday'],
                    'wednesday' => $data['wednesday'],
                    'thursday' => $data['thursday'],
                    'friday' => $data['friday'],
                    'saturday' => $data['saturday'],
                    'sunday' => $data['sunday'],
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
                    'classroom_id' => 1,
                    'course_id' => 1,
                    'starts_at' => '2019-07-01',
                    'ends_at' => '2019-12-31',
                    'duration' => 60,
                    'monday' => '11:00',
                    'tuesday' => '11:00',
                    'wednesday' => '11:00',
                    'thursday' => '11:00',
                    'friday' => '11:00',
                    'saturday' => '11:00',
                    'sunday' => '11:00',
                ]
            ],
            [
                [
                    'branch_id' => 1,
                    'course_id' => 1,
                    'starts_at' => '2019-07-01',
                    'ends_at' => '2019-12-31',
                    'duration' => 60,
                    'monday' => '11:00',
                    'tuesday' => '11:00',
                    'wednesday' => '11:00',
                    'thursday' => '11:00',
                    'friday' => '11:00',
                    'saturday' => '11:00',
                    'sunday' => '11:00',
                ]
            ],
            [
                [
                    'branch_id' => 1,
                    'classroom_id' => 1,
                    'starts_at' => '2019-07-01',
                    'ends_at' => '2019-12-31',
                    'duration' => 60,
                    'monday' => '11:00',
                    'tuesday' => '11:00',
                    'wednesday' => '11:00',
                    'thursday' => '11:00',
                    'friday' => '11:00',
                    'saturday' => '11:00',
                    'sunday' => '11:00',
                ]
            ],
            [
                [
                    'branch_id' => 1,
                    'classroom_id' => 1,
                    'course_id' => 1,
                    'starts_at' => '2019-07-01',
                    'ends_at' => '2019-12-31',
                    'monday' => '11:00',
                    'tuesday' => '11:00',
                    'wednesday' => '11:00',
                    'thursday' => '11:00',
                    'friday' => '11:00',
                    'saturday' => '11:00',
                    'sunday' => '11:00',
                ]
            ]
        ];
    }
}
