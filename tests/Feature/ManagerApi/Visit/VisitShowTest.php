<?php
/**
 * File: VisitShowTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Lesson;

use App\Models\Visit;
use App\Services\Permissions\VisitsPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class LessonShowTest
 * @package Tests\Feature\Api\Lesson
 */
class VisitShowTest extends TestCase
{
    use CreatesFakes;

    protected const URL = 'manager_api/v1/visits';

    private const JSON_STRUCTURE = [
        'data' => [
            'id',
            'student',
            'manager',
            'lesson',
            'event_type',
            'payment_type',
            'created_at',
        ]
    ];

    /**
     * @var Visit
     */
    private $visit;

    /**
     * @var string
     */
    private $url;

    public function setUp(): void
    {
        parent::setUp();
        $instructor = $this->createFakeInstructor();
        $course = $this->createFakeCourse(['instructor_id' => $instructor->id]);
        $schedule = $this->createFakeSchedule(['course_id' => $course->id]);
        $lesson = $this->createFakeLesson([
            'course_id' => $course->id,
            'instructor_id' => $instructor->id,
            'schedule_id' => $schedule->id,
            'controller_id' => null
        ]);
        $student = $this->createFakeStudent();
        $this->visit = $this->createFakeLessonVisit($lesson, $student, null, ['manager_id' => null]);
        $this->url = self::URL . '/' . $this->visit->id;
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
            VisitsPermissions::READ_VISITS
        ]);

        $this
            ->actingAs($user, 'api')
            ->get($this->url)
            ->assertOk()
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'id' => $this->visit->id,
                    'event_type' => 'Lesson',
                    'payment_type' => 'Payment',
                    'created_at' => $this->visit->created_at->toDateTimeString(),
                ]
            ]);
    }
}
