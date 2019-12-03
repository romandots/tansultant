<?php
/**
 * File: CreatesFakeLesson.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Lesson;
use App\Models\Schedule;

/**
 * Trait CreatesFakeLesson
 * @package Tests\Traits
 */
trait CreatesFakeLesson
{
    /**
     * @param array|null $attributes
     * @param Course|null $course
     * @param Classroom|null $classroom
     * @param Branch|null $branch
     * @param Instructor|null $instructor
     * @param Schedule|null $schedule
     * @return Lesson
     */
    private function createFakeLesson(
        ?array $attributes = [],
        ?Course $course = null,
        ?Classroom $classroom = null,
        ?Branch $branch = null,
        ?Instructor $instructor = null,
        ?Schedule $schedule = null
    ): Lesson {
        if (!isset($attributes['instructor_id'])) {
            $instructor = $instructor ?? $this->createFakeInstructor();
            $attributes['instructor_id'] = $attributes['instructor_id'] ?? $instructor->id;
        }

        if (!isset($attributes['course_id'])) {
            $course = $course ?? $this->createFakeCourse([], $instructor ?? null);
            $attributes['course_id'] = $attributes['course_id'] ?? $course->id;
        }

        if (!isset($attributes['branch_id'])) {
            $branch = $branch ?? $this->createFakeBranch();
            $attributes['branch_id'] = $attributes['branch_id'] ?? $branch->id;
        }

        if (!isset($attributes['classroom_id'])) {
            $classroom = $classroom ?? $this->createFakeClassroom([], $branch ?? null);
            $attributes['classroom_id'] = $attributes['classroom_id'] ?? $classroom->id;
        }

        if (!isset($attributes['schedule_id'])) {
            $schedule = $schedule ?? $this->createFakeSchedule([], $course ?? null, $classroom ?? null);
            $attributes['schedule_id'] = $attributes['schedule_id'] ?? $schedule->id;
        }

        return \factory(Lesson::class)->create($attributes);
    }
}
