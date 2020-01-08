<?php
/**
 * File: ScheduleOnDateTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-3
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\PublicApi\Schedule;

use App\Models\Course;
use App\Models\Schedule;
use App\Services\Permissions\SchedulesPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class ScheduleOnDateTest
 * @package Tests\Feature\Api\Schedule
 */
class ScheduleOnDateTest extends TestCase
{
    use CreatesFakes;

    protected const URL = 'schedule/date';
    public const JSON_STRUCTURE = [
        'data' => [
            [
                'id',
                'branch_id',
                'classroom_id',
                'course',
                'starts_at',
                'ends_at',
                'weekday',
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

    /**
     * @param array $params
     * @dataProvider provideInvalidData
     */
    public function testValidationErrors(array $params): void
    {
        $this
            ->getJson($this->getUrl($params))
            ->assertStatus(422);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser([], [
            SchedulesPermissions::READ
        ]);

        $instructor = $this->createFakeInstructor();

        $branchId1 = $this->createFakeBranch()->id;
        $branchId2 = $this->createFakeBranch()->id;
        $branchId3 = $this->createFakeBranch()->id;
        $classroomId = $this->createFakeClassroom()->id;

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
            'starts_at' => '9:00',
            'ends_at' => '10:00',
            'weekday' => 'monday',
            'branch_id' => $branchId1,
            'classroom_id' => $classroomId
        ]);
        $schedule1_2 = $this->createFakeSchedule([
            'course_id' => $course1->id,
            'starts_at' => '11:00',
            'ends_at' => '12:00',
            'weekday' => 'monday',
            'branch_id' => $branchId1,
            'classroom_id' => $classroomId
        ]);
        $schedule1_4 = $this->createFakeSchedule([
            'course_id' => $course1->id,
            'starts_at' => '11:00',
            'ends_at' => '12:00',
            'weekday' => 'tuesday',
            'branch_id' => $branchId1,
            'classroom_id' => $classroomId
        ]);
        $schedule1_3 = $this->createFakeSchedule([
            'course_id' => $course1->id,
            'weekday' => 'tuesday',
            'starts_at' => '13:00',
            'ends_at' => '14:00',
            'branch_id' => $branchId1,
            'classroom_id' => $classroomId
        ]);

        $schedule2_1 = $this->createFakeSchedule([
            'course_id' => $course2->id,
            'starts_at' => '11:00',
            'ends_at' => '12:00',
            'weekday' => 'monday',
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);
        $schedule2_2 = $this->createFakeSchedule([
            'course_id' => $course2->id,
            'starts_at' => '12:00',
            'ends_at' => '13:00',
            'weekday' => 'monday',
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);
        $schedule2_4 = $this->createFakeSchedule([
            'course_id' => $course2->id,
            'starts_at' => '12:00',
            'ends_at' => '13:00',
            'weekday' => 'tuesday',
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);
        $schedule2_3 = $this->createFakeSchedule([
            'course_id' => $course2->id,
            'starts_at' => '13:30',
            'ends_at' => '14:30',
            'weekday' => 'tuesday',
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);

        $schedule3_1 = $this->createFakeSchedule([
            'course_id' => $course3->id,
            'starts_at' => '11:00',
            'ends_at' => '12:00',
            'weekday' => 'monday',
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);
        $schedule3_2 = $this->createFakeSchedule([
            'course_id' => $course3->id,
            'starts_at' => '12:00',
            'ends_at' => '13:00',
            'weekday' => 'monday',
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
                    'id' => $schedule1_4->id
                ],
                [
                    'id' => $schedule2_4->id // ordered
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
                [
                    'date' => 'not date at all'
                ],
            ],
            [
                [
                    'date' => '2019-09-01',
                    'branch_id' => 1
                ],
            ],
            [
                [
                    'date' => '2019-09-01',
                    'classroom_id' => 1
                ],
            ],
            [
                [
                    'date' => '2019-09-01',
                    'course_id' => 1
                ],
            ]
        ];
    }
}
