<?php
/**
 * File: LessonOnDateTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Lesson;

use App\Models\Course;
use App\Models\Lesson;
use App\Services\Permissions\LessonsPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class LessonOnDateTest
 * @package Tests\Feature\Api\Lesson
 */
class LessonOnDateTest extends TestCase
{
    use CreatesFakes;

    protected const URL = 'manager_api/v1/lessons/date';
    public const JSON_STRUCTURE = [
        'data' => [
            [
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
        $user = $this->createFakeManagerUser([], [
            LessonsPermissions::READ_LESSONS
        ]);

        $this
            ->actingAs($user, 'api')
            ->get($this->getUrl($params))
            ->assertStatus(422);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser([], [
            LessonsPermissions::READ_LESSONS
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

        $lesson1_1 = $this->createFakeLesson([
            'schedule_id' => null,
            'instructor_id' => $course1->instructor_id,
            'course_id' => $course1->id,
            'starts_at' => '2019-09-09 10:00:00',
            'ends_at' => '2019-09-09 11:00:00',
            'branch_id' => $branchId1,
            'classroom_id' => $classroomId
        ]);
        $lesson1_2 = $this->createFakeLesson([
            'schedule_id' => null,
            'instructor_id' => $course1->instructor_id,
            'course_id' => $course1->id,
            'starts_at' => '2019-09-09 10:00:01',
            'ends_at' => '2019-09-09 11:00:00',
            'branch_id' => $branchId1,
            'classroom_id' => $classroomId
        ]);
        $lesson1_3 = $this->createFakeLesson([
            'schedule_id' => null,
            'instructor_id' => $course1->instructor_id,
            'course_id' => $course1->id,
            'starts_at' => '2019-09-13 10:00:00',
            'ends_at' => '2019-09-13 11:00:00',
            'branch_id' => $branchId1,
            'classroom_id' => $classroomId
        ]);

        $lesson2_1 = $this->createFakeLesson([
            'schedule_id' => null,
            'instructor_id' => $course2->instructor_id,
            'course_id' => $course2->id,
            'starts_at' => '2019-09-09 10:00:00',
            'ends_at' => '2019-09-09 11:00:00',
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);
        $lesson2_2 = $this->createFakeLesson([
            'schedule_id' => null,
            'instructor_id' => $course2->instructor_id,
            'course_id' => $course2->id,
            'starts_at' => '2019-09-09 10:00:00',
            'ends_at' => '2019-09-09 11:00:00',
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);
        $lesson2_3 = $this->createFakeLesson([
            'schedule_id' => null,
            'instructor_id' => $course2->instructor_id,
            'course_id' => $course2->id,
            'starts_at' => '2019-09-13 10:00:00',
            'ends_at' => '2019-09-13 11:00:00',
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);

        $lesson3_1 = $this->createFakeLesson([
            'schedule_id' => null,
            'instructor_id' => $course3->instructor_id,
            'course_id' => $course3->id,
            'starts_at' => '2019-09-09 10:00:00',
            'ends_at' => '2019-09-09 11:00:00',
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);
        $lesson3_2 = $this->createFakeLesson([
            'schedule_id' => null,
            'instructor_id' => $course3->instructor_id,
            'course_id' => $course3->id,
            'starts_at' => '2019-09-09 10:00:00',
            'ends_at' => '2019-09-09 11:00:00',
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);
        $lesson3_3 = $this->createFakeLesson([
            'schedule_id' => null,
            'instructor_id' => $course3->instructor_id,
            'course_id' => $course3->id,
            'starts_at' => '2019-09-13 10:00:00',
            'ends_at' => '2019-09-13 11:00:00',
            'branch_id' => $branchId2,
            'classroom_id' => $classroomId
        ]);

        $params = [
            'date' => '2019-09-09',
            'branch_id' => $branchId1
        ];
        $count = 2;
        $result = [
            'data' => [
                [
                    'id' => $lesson1_1->id
                ],
                [
                    'id' => $lesson1_2->id
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
