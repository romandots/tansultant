<?php

namespace App\Models\Enum;

enum TransactionStatus: string
{
    case PENDING = 'pending';
    case EXPIRED = 'expired';
    case CONFIRMED = 'confirmed';
    case CANCELED = 'canceled';
}