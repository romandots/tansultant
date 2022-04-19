<?php

namespace App\Models\Enum;

enum ContractStatus: string
{
    case PENDING = 'pending';
    case SIGNED = 'signed';
    case TERMINATED = 'terminated';
}