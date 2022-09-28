<?php

namespace App\Models\Enum;

use App\Common\Contracts\ClassBackedEnum;

enum PaymentObjectType: string implements ClassBackedEnum
{
    case SUBSCRIPTION = 'subscription';
    case LESSON = 'lesson';
    case VISIT = 'visit';

    public function getClass(): string
    {
        return match ($this) {
            self::SUBSCRIPTION => \App\Models\Subscription::class,
            self::LESSON => \App\Models\Lesson::class,
            self::VISIT => \App\Models\Visit::class,
        };
    }
}