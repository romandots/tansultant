<?php
/**
 * File: ScheduleOnDateTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-25
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Schedule;

use App\Models\Course;
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
 * Class ScheduleOnDateTest
 * @package Tests\Feature\Api\Schedule
 */
class ScheduleOnDateTest extends TestCase
{
    use CreatesFakeUser, CreatesFakeSchedule, CreatesFakeInstructor, CreatesFakeCourse, CreatesFakePerson, WithFaker;

    protected const URL = '/api/schedules/date';
    public const JSON_STRUCTURE = [
        'data' => [
            [
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
    }

    /**
     * @param array $params
     * @return string
     */
    private function getUrl(array $params = []): string
    {
        $queryString = \http_build_query($params);
        return self::URL . ($params !== [] ? '?' . $queryString : null);
    }

    public function testAccessDenied(): void
    {
        $this
            ->get($this->getUrl())
            ->assertStatus(401);
    }

    public function testNoPermission(): void
    {
        $user = $this->createFakeUser();

        $this
            ->actingAs($user, 'api')
            ->get($this->getUrl())
            ->assertStatus(403);
    }

    /**
     * @param array $params
     * @dataProvider provideInvalidData
     */
    public function testValidationErrors(array $params): void
    {
        $user = $this->createFakeUser([], [
            SchedulesPermissions::READ_SCHEDULES
        ]);

        $this
            ->actingAs($user, 'api')
            ->get($this->getUrl($params))
            ->assertStatus(422);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeUser([], [
            SchedulesPermissions::READ_SCHEDULES
        ]);

        $instructor = $this->createFakeInstructor();

        $branchId1 = $this->faker->unique()->numerify('########');
        $branchId2 = $this->faker->unique()->numerify('########');
        $branchId3 = $this->faker->unique()->numerify('########');
        $classroomId = $this->faker->unique()->numerify('########');

        $course1 = $this->createFakeCourse([
            'instructor_id' => $instructor->id,
            'starts_at' => null,
            'ends_at' => null,
            'status' => Course::STATUS_ACTIVE
        ]);
        $course2 = $this->createFakeCourse([
            'instructor_id' => $instructor->id,
            'starts_at' => null,
            'ends_at' => null,
            'status' => Course::STATUS_ACTIVE
        ]);
        $course3 = $this->createFakeCourse([
            'instructor_id' => $instructor->id,
            'starts_at' => null,
            'ends_at' => null,
            'status' => Course::STATUS_DISABLED
        ]);

        $schedule1_1 = $this->createFakeSchedule([
            'course_id' => $course1->id,
            'monday' => '11:00',
            'tuesday' => null,
            'branch_id' => $branchId1,
            'classroom_id' => $classroomId
        ]);
        $schedule1_2 = $this->createFakeSchedule([
            'course_id' => $course1->id,
            'monday' => '12:00',
            'tuesday' => '12:00',
            'branch_id' => $branchId1,
            'classroom_id' => $classroomId
        ]);
        $schedule1_3 = $this->createFakeSchedule([
            'course_id' => $course1->id,
            'monday' => null,
            'tuesday' => '13:00',
            'branch_id' => $branchId1,
            'classroom_id' => $classroomId
        ]);

        $schedule2_1 = $this->createFakeSchedule([
            'course_id' => $course2->id,
            'monday' => '11:00',
            'tuesday' => null,
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);
        $schedule2_2 = $this->createFakeSchedule([
            'course_id' => $course2->id,
            'monday' => '12:00',
            'tuesday' => '12:00',
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);
        $schedule2_3 = $this->createFakeSchedule([
            'course_id' => $course2->id,
            'monday' => null,
            'tuesday' => '13:00',
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);

        $schedule3_1 = $this->createFakeSchedule([
            'course_id' => $course3->id,
            'monday' => '11:00',
            'tuesday' => null,
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);
        $schedule3_2 = $this->createFakeSchedule([
            'course_id' => $course3->id,
            'monday' => '12:00',
            'tuesday' => '12:00',
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);
        $schedule3_3 = $this->createFakeSchedule([
            'course_id' => $course3->id,
            'monday' => null,
            'tuesday' => '13:00',
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);

        $params = [
            'date' => '2019-07-22', // monday
            'branch_id' => $branchId1
        ];
        $count = 2;
        $result = [
            'data' => [
                [
                    'id' => $schedule1_1->id
                ],
                [
                    'id' => $schedule1_2->id
                ],
            ]
        ];

        $this
            ->actingAs($user, 'api')
            ->get($this->getUrl($params))
            ->assertOk()
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount($count, 'data')
            ->assertJson($result);

        $params = [
            'date' => '2019-07-23', // tuesday
            'classroom_id' => $classroomId
        ];

        $count = 4;
        $result = [
            'data' => [
                [
                    'id' => $schedule1_2->id
                ],
                [
                    'id' => $schedule2_2->id // ordered
                ],
                [
                    'id' => $schedule1_3->id // by time
                ],
                [
                    'id' => $schedule2_3->id
                ],
            ]
        ];

        $this
            ->actingAs($user, 'api')
            ->get($this->getUrl($params))
            ->assertOk()
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount($count, 'data')
            ->assertJson($result);

        $params = [
            'date' => '2019-07-22', // monday
            'course_id' => $course3->id
        ];

        $count = 0;
        $result = [
            'data' => []
        ];

        $this
            ->actingAs($user, 'api')
            ->get($this->getUrl($params))
            ->assertOk()
            ->assertJsonCount($count, 'data')
            ->assertJson($result);
    }

    /**
     * @return array
     */
    public function provideInvalidData(): array
    {
        return [
            [
                [],
            ],
            [
                [
                    'date' => 'not date at all'
                ],
            ],
            [
                [
                    'date' => '2019-09-01',
                    'branch_id' => 'string'
                ],
            ],
            [
                [
                    'date' => '2019-09-01',
                    'classroom_id' => 'string'
                ],
            ],
            [
                [
                    'date' => '2019-09-01',
                    'course_id' => 'string'
                ],
            ]
        ];
    }
}
