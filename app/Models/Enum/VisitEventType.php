<?php

namespace App\Models\Enum;

use App\Common\Contracts\ClassBackedEnum;

enum VisitEventType: string implements ClassBackedEnum
{
    case LESSON = 'lesson';
    case EVENT = 'event';

    public function getClass(): string
    {
        return match ($this) {
            self::LESSON => \App\Models\Lesson::class,
            self::EVENT => throw new \Exception('To be implemented'),
        };
    }
}