<?php

namespace App\Models\Enum;

enum BonusStatus: string
{
    case PENDING = 'pending';
    case EXPIRED = 'expired';
    case ACTIVATED = 'activated';
    case CANCELED = 'canceled';
}