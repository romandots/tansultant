<?php

namespace App\Models\Enum;

enum IntentEventType: string
{
    case LESSON = 'lesson';
    case EVENT = 'event';
}