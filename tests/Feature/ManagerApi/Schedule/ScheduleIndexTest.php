<?php
/**
 * File: ScheduleIndexTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Schedule;

use App\Models\Course;
use App\Models\Schedule;
use App\Services\Permissions\SchedulesPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class ScheduleIndexTest
 * @package Tests\Feature\Api\Schedule
 */
class ScheduleIndexTest extends TestCase
{
    use CreatesFakes;

    /**
     * @var Schedule[]
     */
    private array $schedules;

    /**
     * @var string
     */
    private string $url;

    /**
     * @var Course
     */
    private Course $course;

    public function setUp(): void
    {
        parent::setUp();
        $this->course = $this->createFakeCourse();
        $this->schedules = \factory(Schedule::class, 3)->create(['course_id' => $this->course->id])->all();
        $this->url = 'admin/courses/' . $this->course->id . '/schedules';
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
        $user = $this->createFakeManagerUser(
            [],
            [
                SchedulesPermissions::READ
            ]
        );

        $expectedData = [];
        foreach ($this->schedules as $schedule) {
            $expectedData[] = [
                'id' => $schedule->id,
                'branch' => ['id' => $schedule->branch_id],
                'classroom' => ['id' => $schedule->classroom_id],
                'starts_at' => \Carbon\Carbon::parse($schedule->starts_at)->format('H:i:00'),
                'ends_at' => \Carbon\Carbon::parse($schedule->ends_at)->format('H:i:00'),
                'weekday' => (string)$schedule->weekday,
            ];
        }

        $this
            ->actingAs($user, 'api')
            ->get($this->url)
            ->assertOk()
            ->assertJson(
                [
                    'data' => $expectedData
                ]
            );
    }
}
