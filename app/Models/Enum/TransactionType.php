<?php

namespace App\Models\Enum;

enum TransactionType: string
{
    case MANUAL = 'manual';
    case AUTO = 'auto';
}