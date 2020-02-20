<?php
/**
 * File: CourseShowTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
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
class CourseShowTest extends TestCase
{
    use CreatesFakeUser, CreatesFakeCourse, CreatesFakeInstructor, CreatesFakePerson;

    protected const URL = 'admin/courses';

    private const JSON_STRUCTURE = [
        'data' => [
            'id',
            'name',
            'summary',
            'description',
            'display',
            'picture',
            'picture_thumb',
            'status',
            'status_label',
            'instructor',
            'age_restrictions',
            'age_restrictions_string',
            'starts_at',
            'ends_at',
        ]
    ];

    /**
     * @var Course
     */
    private $course;

    /**
     * @var string
     */
    private $url;

    public function setUp(): void
    {
        parent::setUp();
        $instructor = $this->createFakeInstructor();
        $this->course = $this->createFakeCourse(['instructor_id' => $instructor->id]);
        $this->url = self::URL . '/' . $this->course->id;
    }

    public function testAccessDenied(): void
    {
        $this
            ->get($this->url)
            ->assertStatus(401);
    }

    public function testNoPermission(): void
    {
        $user = $this->createFakeUser();

        $this
            ->actingAs($user, 'api')
            ->get($this->url)
            ->assertStatus(403);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser([], [
            CoursesPermissions::READ
        ]);

        $this
            ->actingAs($user, 'api')
            ->get($this->url)
            ->assertOk()
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'id' => $this->course->id,
                    'name' => $this->course->name,
                    'summary' => $this->course->summary,
                    'description' => $this->course->description,
                    'picture' => $this->course->picture,
                    'picture_thumb' => $this->course->picture_thumb,
                    'status' => $this->course->status,
                ]
            ]);
    }
}
