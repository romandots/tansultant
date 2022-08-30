<?php

namespace App\Models\Enum;

enum LogRecordAction: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case RESTORE = 'restore';
    case ENABLE = 'enable';
    case DISABLE = 'disable';
    case SEND = 'send';
    case OPEN = 'open';
    case CLOSE = 'close';
    case BOOK = 'book';
    case CANCEL = 'cancel';
    case CHECKOUT = 'checkout';
}