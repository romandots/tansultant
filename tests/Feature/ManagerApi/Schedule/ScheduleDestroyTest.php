<?php
/**
 * File: CourseDestroyTest.php
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
 * Class ScheduleDestroyTest
 * @package Tests\Feature\Api\Schedule
 */
class ScheduleDestroyTest extends TestCase
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
        $this->artisan('db:seed');
        $this->course = $this->createFakeCourse();
        $this->schedules = Schedule::factory()->create(['course_id' => $this->course->id]);
        $this->url = 'admin/schedules/' . $this->schedules[0]->id;
    }

    public function testAccessDenied(): void
    {
        $this
            ->delete($this->url)
            ->assertStatus(401);
    }

    public function testNoPermission(): void
    {
        $user = $this->createFakeUser();

        $this
            ->actingAs($user, 'api')
            ->delete($this->url)
            ->assertStatus(403);
    }

    public function testSuccess(): void
    {
        $user = $this->createFakeManagerUser(
            [],
            [
                SchedulesPermissions::DELETE
            ]
        );

        $this
            ->actingAs($user, 'api')
            ->delete($this->url)
            ->assertOk();

        $this->assertDatabaseMissing(
            Schedule::TABLE,
            [
                'id' => $this->schedules[0]->id,
                'deleted_at' => null
            ]
        );
    }
}
