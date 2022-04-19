<?php

namespace App\Models\Enum;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case EXPIRED = 'expired';
    case CONFIRMED = 'confirmed';
    case CANCELED = 'canceled';
}