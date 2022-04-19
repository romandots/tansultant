<?php

namespace App\Models\Enum;

enum IntentStatus: string
{
    case EXPECTING = 'expecting';
    case VISITED = 'visited';
    case NOSHOW = 'no-show';
}