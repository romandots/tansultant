<?php
/**
 * File: CourseStoreTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Course;

use App\Models\Course;
use App\Models\Enum\InstructorStatus;
use App\Models\Instructor;
use App\Services\Permissions\CoursesPermissions;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\CreatesFakeCourse;
use Tests\Traits\CreatesFakeInstructor;
use Tests\Traits\CreatesFakePerson;
use Tests\Traits\CreatesFakeUser;

/**
 * Class CourseStoreTest
 * @package Tests\Feature\Api\Course
 */
class CourseStoreTest extends TestCase
{
    use CreatesFakeCourse;
    use CreatesFakeInstructor;
    use CreatesFakePerson;
    use CreatesFakeUser;

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
            'age_restrictions_from',
            'age_restrictions_to',
            'instructor',
            'starts_at',
            'ends_at',
            'genres',
        ]
    ];

    /**
     * @var Course
     */
    private Course $course;

    /**
     * @var string
     */
    private string $url;

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
        $this->artisan('db:seed');
        $user = $this->createFakeManagerUser(
            [],
            [
                CoursesPermissions::CREATE
            ]
        );

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(422);
    }

    public function testWithFiredInstructor(): void
    {
        $this->artisan('db:seed');
        $user = $this->createFakeManagerUser(
            [],
            [
                CoursesPermissions::CREATE
            ]
        );

        $instructor = $this->createFakeInstructor(['status' => InstructorStatus::FIRED]);
        $data = [
            'name' => $this->faker->name,
            'status' => CourseStatus::ACTIVE,
            'summary' => $this->faker->words(10, true),
            'description' => $this->faker->words(10, true),
            'picture' => null,
            'age_restrictions' => '3+',
            'instructor_id' => $instructor->id,
            'starts_at' => Carbon::now()->toDateString(),
            'ends_at' => null,
        ];

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(409)
            ->assertJson(
                [
                    'error' => 'instructor_status_incompatible',
                    'message' => 'Недопустимый статус инструктора',
                    'data' => [
                        'instructor' => [
                            'id' => $instructor->id,
                            'name' => $instructor->name,
                            'status' => InstructorStatus::FIRED,
                        ]
                    ]
                ]
            );
    }

    public function testSuccess(): void
    {
        $this->artisan('db:seed');
        $user = $this->createFakeManagerUser(
            [],
            [
                CoursesPermissions::CREATE
            ]
        );

        $instructor = $this->createFakeInstructor();
        $data = [
            'name' => $this->faker->name,
            'status' => CourseStatus::ACTIVE,
            'summary' => $this->faker->words(10, true),
            'description' => $this->faker->words(10, true),
            'picture' => null,
            'age_restrictions_from' => 3,
            'instructor_id' => $instructor->id,
            'starts_at' => Carbon::now()->toDateString(),
            'ends_at' => null,
            'genres' => ['vogue', 'waacking'],
        ];

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(201)
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson(
                [
                    'data' => [
                        'name' => $data['name'],
                        'summary' => $data['summary'],
                        'description' => $data['description'],
                        'picture' => $data['picture'],
                        'status' => $data['status'],
                    ]
                ]
            );
    }

    /**
     * @return array
     */
    public function provideInvalidData(): array
    {
        return [
            [
                [
                    'name' => 'Название',
                    'status' => CourseStatus::ACTIVE,
                    'instructor_id' => '$instructor->id',
                ]
            ],
            [
                [
                    'status' => CourseStatus::ACTIVE,
                ]
            ],
            [
                [
                    'status' => 'wrong',
                ]
            ],
            [
                [
                    'name' => 'Название',
                ]
            ]
        ];
    }
}
