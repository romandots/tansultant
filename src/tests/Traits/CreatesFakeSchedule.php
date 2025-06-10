<?php
/**
 * File: CreatesFakeSchedule.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Classroom;
use App\Models\Course;
use App\Models\Schedule;

/**
 * Class CreatesFakeSchedule
 * @package Tests\Traits
 */
trait CreatesFakeSchedule
{
    /**
     * @param array $attributes
     * @param Course|null $course
     * @param Classroom|null $classroom
     * @return Schedule
     */
    protected function createFakeSchedule(
        array $attributes = [],
        ?Course $course = null,
        ?Classroom $classroom = null
    ):
    Schedule {
        if (!isset($attributes['course_id'])) {
            $course = $course ?? $this->createFakeCourse();
            $attributes['course_id'] = $attributes['course_id'] ?? $course->id;
        }

        if (!isset($attributes['classroom_id']) && !isset($attributes['branch_id'])) {
            $classroom = $classroom ?? $this->createFakeClassroom();
            $attributes['classroom_id'] = $attributes['classroom_id'] ?? $classroom->id;
            $attributes['branch_id'] = $attributes['branch_id'] ?? $classroom->branch_id;
        }

        return Schedule::factory()->create($attributes);
    }
}
