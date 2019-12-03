<?php
/**
 * File: CreatesFakeCourse.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Course;
use App\Models\Instructor;

/**
 * Trait CreatesFakeCourse
 * @package Tests\Traits
 */
trait CreatesFakeCourse
{
    /**
     * @param array $attributes
     * @param Instructor|null $instructor
     * @return Course
     */
    private function createFakeCourse(array $attributes = [], ?Instructor $instructor = null): Course
    {
        if (!isset($attributes['instructor_id'])) {
            $instructor = $instructor ?? $this->createFakeInstructor();
            $attributes['instructor_id'] = $attributes['instructor_id'] ?? $instructor->id;
        }
        return \factory(Course::class)->create($attributes);
    }
}
