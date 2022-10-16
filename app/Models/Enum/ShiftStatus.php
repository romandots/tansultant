<?php

namespace App\Models\Enum;

enum ShiftStatus: string
{
    case ACTIVE = 'active';
    case CLOSED = 'closed';
}