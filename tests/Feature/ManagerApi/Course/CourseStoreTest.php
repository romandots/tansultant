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
    use WithFaker;

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
        $user = $this->createFakeManagerUser([], [
            CoursesPermissions::CREATE
        ]);

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(422);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser([], [
            CoursesPermissions::CREATE
        ]);

        $instructor = $this->createFakeInstructor();
        $data = [
            'name' => $this->faker->name,
            'status' => Course::STATUS_ACTIVE,
            'summary' => $this->faker->sentence,
            'description' => $this->faker->text,
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
            ->assertJson([
                'data' => [
                    'name' => $data['name'],
                    'summary' => $data['summary'],
                    'description' => $data['description'],
                    'picture' => $data['picture'],
                    'status' => $data['status'],
                    'age_restrictions_from' => $data['age_restrictions_from'],
                    'age_restrictions_to' => null,
                    'genres' => ['vogue', 'waacking'],
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
                    'name' => 'Название',
                    'status' => Course::STATUS_ACTIVE,
                    'instructor_id' => '$instructor->id',
                ]
            ],
            [
                [
                    'status' => Course::STATUS_ACTIVE,
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
