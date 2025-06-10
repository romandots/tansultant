<?php

namespace App\Models\Enum;

enum SubscriptionStatus: string
{
    case NOT_PAID = 'not_paid';
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case ON_HOLD = 'on_hold';
    case EXPIRED = 'expired';
    case CANCELED = 'canceled';
}