<?php

namespace App\Models\Enum;

use App\Common\Contracts\ClassBackedEnum;

enum VisitPaymentType: string implements ClassBackedEnum
{
    case PAYMENT = 'payment';
    case PROMOCODE = 'promocode';

    public function getClass(): string
    {
        return match ($this) {
            self::PAYMENT => \App\Models\Payment::class,
            self::PROMOCODE => throw new \Exception('To be implemented'),
        };
    }
}