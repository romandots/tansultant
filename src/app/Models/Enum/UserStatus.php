<?php

namespace App\Models\Enum;

enum UserStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case DISABLED = 'disabled';
}