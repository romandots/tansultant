<?php

namespace App\Models\Enum;

enum CourseStatus: string
{
    case STATUS_PENDING = 'pending';
    case STATUS_ACTIVE = 'active';
    case STATUS_DISABLED = 'disabled';
}