<?php

namespace App\Models\Enum;

enum LessonStatus: string
{
    case BOOKED = 'booked';
    case ONGOING = 'ongoing';
    case PASSED = 'passed';
    case CANCELED = 'canceled';
    case CLOSED = 'closed';
    case CHECKED_OUT = 'checked_out';
}