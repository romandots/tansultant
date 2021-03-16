<?php
/**
 * File: CourseDisabledEvent.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-9
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Events;

use App\Models\Course;

class CourseDisabledEvent extends BaseEvent
{
    public Course $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }
}
