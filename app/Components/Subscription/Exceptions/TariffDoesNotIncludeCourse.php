<?php

namespace App\Components\Subscription\Exceptions;

use App\Models\Course;
use App\Models\Tariff;

class TariffDoesNotIncludeCourse extends Exception
{
    public function __construct(
        public readonly Tariff $tariff,
        public readonly Course $course
    ) {
        parent::__construct('tariff_does_not_include_course', [
            'course_id' => $this->course->id,
            'course' => $this->course->name,
            'tariff_id' => $this->tariff->id,
            'tariff' => $this->tariff->name,
        ], 409);
    }
}