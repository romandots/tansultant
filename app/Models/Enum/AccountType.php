<?php

namespace App\Models\Enum;

enum AccountType: string
{
    case OPERATIONAL = 'operational';
    case SAVINGS = 'savings';
}