<?php

namespace App\Models\Enum;

enum StudentStatus: string
{
    case POTENTIAL = 'potential';
    case ACTIVE = 'active';
    case RECENT = 'recent';
    case FORMER = 'former';
}