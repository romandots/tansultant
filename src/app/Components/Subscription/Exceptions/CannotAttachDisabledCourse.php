<?php

namespace App\Components\Subscription\Exceptions;

use App\Exceptions\SimpleValidationException;
use App\Models\Course;

class CannotAttachDisabledCourse extends SimpleValidationException
{
    protected Course $course;

    public function __construct(Course $course)
    {
        parent::__construct('courses', 'disabled');
        $this->course = $course;
    }
}