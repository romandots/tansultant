<?php

namespace App\Models\Enum;

enum PayoutStatus: string
{
    case CREATED = 'created';
    case PREPARED = 'prepared';
    case PAID = 'paid';
}