<?php
/**
 * File: CourseEnableTest.php
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
 * @package Tests\Feature\Api\Course
 */
class CourseEnableTest extends TestCase
{
    use CreatesFakeUser, CreatesFakeCourse, CreatesFakeInstructor, CreatesFakePerson;

    protected const URL = 'admin/courses';

    private const JSON_STRUCTURE = [
        'data' => [
            'id',
            'name',
            'summary',
            'description',
            'picture',
            'picture_thumb',
            'status',
            'status_label',
            'instructor',
            'starts_at',
            'ends_at'
        ]
    ];

    /**
     * @var Course
     */
    private Course $deletedCourse;

    /**
     * @var string
     */
    private string $url;

    public function setUp(): void
    {
        parent::setUp();
        $instructor = $this->createFakeInstructor();
        $this->deletedCourse = $this->createFakeCourse([
            'instructor_id' => $instructor->id,
            'status' => CourseStatus::DISABLED
        ]);
        $this->url = self::URL . '/' . $this->deletedCourse->id . '/enable';
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

    public function testSuccess(): void
    {
        $this->artisan('db:seed');
        $user = $this->createFakeManagerUser([], [
            CoursesPermissions::ENABLE
        ]);

        $this->assertDatabaseHas(Course::TABLE, [
            'id' => $this->deletedCourse->id,
        ]);

        $this
            ->actingAs($user, 'api')
            ->post($this->url)
            ->assertOk();

        $this->assertDatabaseHas(Course::TABLE, [
            'id' => $this->deletedCourse->id,
            'status' => CourseStatus::ACTIVE
        ]);
    }
}
