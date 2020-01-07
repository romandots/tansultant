<?php
/**
 * File: CourseStoreTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Schedule;

use App\Models\Schedule;
use App\Services\Permissions\SchedulesPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class ScheduleStoreTest
 * @package Tests\Feature\Api\Schedule
 */
class ScheduleStoreTest extends TestCase
{
    use CreatesFakes;

    protected const URL = 'admin/schedules';

    private const JSON_STRUCTURE = [
        'data' => [
            'id',
            'branch_id',
            'classroom_id',
            'course',
            'starts_at',
            'ends_at',
            'weekday',
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
            SchedulesPermissions::CREATE
        ]);

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(422);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser([], [
            SchedulesPermissions::CREATE
        ]);

        $course = $this->createFakeCourse();

        $data = [
            'branch_id' => $this->createFakeBranch()->id,
            'classroom_id' => $this->createFakeClassroom()->id,
            'course_id' => $course->id,
            'starts_at' => $this->faker->time('H:i:00'),
            'ends_at' => $this->faker->time('H:i:00'),
            'weekday' => 'monday',
        ];

        $responseData = $data;
        $responseData['course'] = ['id' => $data['course_id']];
        unset($responseData['course_id']);

        $this
            ->actingAs($user, 'api')
            ->post($this->url, $data)
            ->assertStatus(201)
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => $responseData
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
