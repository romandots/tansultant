<?php

namespace App\Models\Enum;

enum PaymentType: string
{
    case MANUAL = 'manual';
    case AUTO = 'auto';
}