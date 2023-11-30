<?php

namespace App\Adapters\Banks\TochkaBank\Enum;

enum QrType: string
{
    case STATIC = '01';
    case DYNAMIC = '02';
}