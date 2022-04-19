<?php

namespace App\Models\Enum;

enum LessonType: string
{
    case LESSON = 'lesson';
    case EVENT = 'event';
    case RENT = 'rent';
}