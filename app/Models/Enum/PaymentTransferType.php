<?php

namespace App\Models\Enum;

enum PaymentTransferType: string
{
    case CASH = 'cash';
    case CARD = 'card';
    case ONLINE = 'online';
    case INTERNAL = 'internal';
    case CODE = 'code';
}