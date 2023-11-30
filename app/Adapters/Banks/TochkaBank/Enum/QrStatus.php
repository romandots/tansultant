<?php

namespace App\Adapters\Banks\TochkaBank\Enum;

enum QrStatus: string
{
    case NOT_STARTED = 'NotStarted';
    case RECEIVED = 'Received';
    case IN_PROGRESS = 'InProgress';
    case ACCEPTED = 'Accepted';
    case REJECTED = 'Rejected';
}