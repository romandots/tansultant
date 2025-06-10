<?php

namespace App\Components\Course\Exceptions;

use App\Exceptions\InvalidStatusException;
use App\Models\Course;
use App\Models\Enum\CourseStatus;

class CannotAttachDisabledCourse extends InvalidStatusException
{
    public function __construct(public readonly Course $course)
    {
        parent::__construct($this->course->status->value, [CourseStatus::PENDING, CourseStatus::ACTIVE]);
    }
}