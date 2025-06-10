<?php

namespace App\Models\Enum;

enum CourseStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case DISABLED = 'disabled';
}