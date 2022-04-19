<?php
/**
 * File: CourseIndexTest.php
 * Author: Roman Dots <romandots@brainex.co>
 * Date: 2020-2-20
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Course;

use App\Models\Course;
use App\Services\Permissions\CoursesPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakeCourse;
use Tests\Traits\CreatesFakeInstructor;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeUser;

/**
 * Class CourseShowTest
 * @package Tests\Feature\Api\Course
 */
class CourseIndexTest extends TestCase
{
    use CreatesFakeUser, CreatesFakeCourse, CreatesFakeInstructor, CreatesFakePerson;

    protected const URL = 'admin/courses';

    /**
     * @var Course[]
     */
    private array $courses;

    /**
     * @var \App\Models\User
     */
    private \App\Models\User $userWithPermission;

    /**
     * @var \App\Models\Instructor
     */
    private \App\Models\Instructor $instructor;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        $this->instructor = $this->createFakeInstructor();
        /** @var \Illuminate\Database\Eloquent\Collection|Course[] $courses */
        $courses = [
            Course::factory()->create(['instructor_id' => $this->instructor->id]),
            Course::factory()->create(['status' => CourseStatus::DISABLED]),
            Course::factory()->create(['status' => CourseStatus::PENDING]),
            Course::factory()->create(['status' => CourseStatus::ACTIVE])
        ];

        $counter = 0;
        foreach ($courses as $course) {
            $course->created_at = $course->created_at->addSeconds($counter);
            $course->updated_at = $course->updated_at->addSeconds($counter);
            $course->save();
            $counter++;
        }

        $this->courses = Course::query()->orderBy('created_at', 'desc')->get()->all();

        $this->userWithPermission = $this->createFakeManagerUser(
            [],
            [
                CoursesPermissions::READ
            ]
        );
    }

    public function testAccessDenied(): void
    {
        $this
            ->get(self::URL)
            ->assertStatus(401);
    }

    public function testNoPermission(): void
    {
        $user = $this->createFakeUser();

        $this
            ->actingAs($user, 'api')
            ->get(self::URL)
            ->assertStatus(403);
    }

    public function testIndex(): void
    {
        $expectedData = [];
        foreach ($this->courses as $course) {
            $expectedData[] = [
                'id' => $course->id,
                'name' => $course->name,
                'summary' => $course->summary,
                'description' => $course->description,
                'picture' => $course->picture,
                'picture_thumb' => $course->picture_thumb,
                'status' => $course->status,
            ];
        }
        $this
            ->actingAs($this->userWithPermission, 'api')
            ->get(self::URL)
            ->assertOk()
            ->assertJsonCount(20, 'data')
            ->assertJson(
                [
                    'data' => $expectedData,
                    'meta' => [
                        'current_page' => 1,
                        'total' => 20,
                    ]
                ]
            );
    }

    /**
     * @param string $sort
     * @param string $order
     * @dataProvider sortProvider
     */
    public function testIndexSorting(string $sort, string $order): void
    {
        $expectedData = [];

        $courses = Course::query()->orderBy($sort, $order)->get();

        foreach ($courses as $course) {
            $expectedData[] = [
                'id' => $course->id,
                'name' => $course->name,
                'summary' => $course->summary,
                'description' => $course->description,
                'picture' => $course->picture,
                'picture_thumb' => $course->picture_thumb,
                'status' => $course->status,
            ];
        }

        $this
            ->actingAs($this->userWithPermission, 'api')
            ->get(self::URL . '?' . \http_build_query(['sort' => $sort, 'order' => $order]))
            ->assertOk()
            ->assertJsonCount(20, 'data')
            ->assertJson(
                [
                    'data' => $expectedData,
                    'meta' => [
                        'current_page' => 1,
                        'total' => 20,
                    ]
                ]
            );
    }

    public function sortProvider(): array
    {
        return [
            'ID' => ['sort' => 'id', 'order' => 'asc'],
            '-ID' => ['sort' => 'id', 'order' => 'desc'],
            'Name' => ['sort' => 'name', 'order' => 'asc'],
            '-Name' => ['sort' => 'name', 'order' => 'desc'],
            'CreatedAt' => ['sort' => 'created_at', 'order' => 'asc'],
            '-CreatedAt' => ['sort' => 'created_at', 'order' => 'desc'],
            'StartsAt' => ['sort' => 'starts_at', 'order' => 'asc'],
            '-StartsAt' => ['sort' => 'starts_at', 'order' => 'desc'],
            'EndsAt' => ['sort' => 'id', 'ends_at' => 'asc'],
            '-EndsAt' => ['sort' => 'id', 'ends_at' => 'desc'],
            'Status' => ['sort' => 'status', 'ends_at' => 'asc'],
            '-Status' => ['sort' => 'status', 'ends_at' => 'desc'],
        ];
    }

    public function testIndexPagination(): void
    {
        $expectedData = [];
        foreach ($this->courses as $course) {
            $expectedData[] = [
                'id' => $course->id,
                'name' => $course->name,
                'summary' => $course->summary,
                'description' => $course->description,
                'picture' => $course->picture,
                'picture_thumb' => $course->picture_thumb,
                'status' => $course->status,
                'instructor' => ['id' => $course->instructor_id],
                'created_at' => $course->created_at->toDateTimeString(),
                'updated_at' => $course->updated_at->toDateTimeString(),
            ];
        }

        $expectedData = \array_slice($expectedData, -5, 5);

        $this
            ->actingAs($this->userWithPermission, 'api')
            ->get(self::URL . '?' . \http_build_query(['page' => 4, 'per_page' => 5]))
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJson(
                [
                    'data' => $expectedData,
                    'meta' => [
                        'current_page' => 4,
                        'per_page' => 5,
                        'last_page' => 4,
                        'total' => 20,
                    ]
                ]
            );
    }

    public function testIndexFilteredByInstructorId(): void
    {
        $expectedData = [];
        foreach ($this->courses as $course) {
            if ($this->instructor->id === $course->instructor_id) {
                $expectedData[] = [
                    'id' => $course->id,
                    'name' => $course->name,
                    'summary' => $course->summary,
                    'description' => $course->description,
                    'picture' => $course->picture,
                    'picture_thumb' => $course->picture_thumb,
                    'status' => $course->status,
                ];
            }
        }
        $this
            ->actingAs($this->userWithPermission, 'api')
            ->get(self::URL . '?' . \http_build_query(['instructors_ids[]' => $this->instructor->id]))
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJson(
                [
                    'data' => $expectedData,
                    'meta' => [
                        'current_page' => 1,
                        'total' => 5,
                    ]
                ]
            );
    }

    /**
     * @dataProvider statusProvider
     * @param string $status
     */
    public function testIndexFilteredByStatus(string $status): void
    {
        $expectedData = [];
        foreach ($this->courses as $course) {
            if ($status === $course->status) {
                $expectedData[] = [
                    'id' => $course->id,
                    'name' => $course->name,
                    'summary' => $course->summary,
                    'description' => $course->description,
                    'picture' => $course->picture,
                    'picture_thumb' => $course->picture_thumb,
                    'status' => $course->status,
                ];
            }
        }
        $totalCount = CourseStatus::ACTIVE === $status ? 10 : 5;
        $this
            ->actingAs($this->userWithPermission, 'api')
            ->get(self::URL . '?' . \http_build_query(['statuses[]' => $status]))
            ->assertOk()
            ->assertJsonCount($totalCount, 'data')
            ->assertJson(
                [
                    'data' => $expectedData,
                    'meta' => [
                        'current_page' => 1,
                        'total' => $totalCount,
                    ]
                ]
            );
    }

    public function statusProvider(): array
    {
        return [
            'STATUS_PENDING' => [CourseStatus::PENDING],
            'STATUS_ACTIVE' => [CourseStatus::ACTIVE],
            'STATUS_DISABLED' => [CourseStatus::DISABLED],
        ];
    }
}
