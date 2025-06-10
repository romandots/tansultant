<?php

namespace App\Models\Enum;

enum TariffStatus: string
{
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
}