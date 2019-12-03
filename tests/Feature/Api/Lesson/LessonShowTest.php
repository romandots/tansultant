<?php
/**
 * File: LessonShowTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Feature\Api\Lesson;

use App\Models\Lesson;
use App\Services\Permissions\LessonsPermissions;
use Tests\TestCase;
use Tests\Traits\CreatesFakes;

/**
 * Class LessonShowTest
 * @package Tests\Feature\Api\Lesson
 */
class LessonShowTest extends TestCase
{
    use CreatesFakes;

    protected const URL = '/lessons';

    private const JSON_STRUCTURE = [
        'data' => [
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
        $instructor = $this->createFakeInstructor();
        $course = $this->createFakeCourse(['instructor_id' => $instructor->id]);
        $schedule = $this->createFakeSchedule(['course_id' => $course->id]);
        $this->lesson = $this->createFakeLesson([
            'course_id' => $course->id,
            'instructor_id' => $instructor->id,
            'schedule_id' => $schedule->id,
            'controller_id' => null
        ]);
        $this->url = self::URL . '/' . $this->lesson->id;
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
            LessonsPermissions::READ_LESSONS
        ]);

        $this
            ->actingAs($user, 'api')
            ->get($this->url)
            ->assertOk()
            ->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJson([
                'data' => [
                    'id' => $this->lesson->id,
                    'name' => $this->lesson->name,
                    'status' => $this->lesson->status,
                    'status_label' => \trans('lesson.' . $this->lesson->status),
                    'type' => $this->lesson->type,
                    'type_label' => \trans('lesson.' . $this->lesson->type),
                    'starts_at' => $this->lesson->starts_at ? $this->lesson->starts_at->toDateTimeString() : null,
                    'ends_at' => $this->lesson->ends_at ? $this->lesson->ends_at->toDateTimeString() : null,
                    'closed_at' => $this->lesson->closed_at ? $this->lesson->closed_at->toDateTimeString() : null,
                    'canceled_at' => $this->lesson->canceled_at ? $this->lesson->canceled_at->toDateTimeString() : null,
                    'created_at' => $this->lesson->created_at ? $this->lesson->created_at->toDateTimeString() : null,
                ]
            ]);
    }
}
