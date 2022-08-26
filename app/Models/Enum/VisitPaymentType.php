<?php

namespace App\Models\Enum;

use App\Common\Contracts\ClassBackedEnum;

enum VisitPaymentType: string implements ClassBackedEnum
{
    case SUBSCRIPTION = 'subscription';
    case PAYMENT = 'payment';
    case PROMOCODE = 'promocode';

    public function getClass(): string
    {
        return match ($this) {
            self::SUBSCRIPTION => \App\Models\Subscription::class,
            self::PAYMENT => \App\Models\Payment::class,
            self::PROMOCODE => throw new \Exception('To be implemented'),
        };
    }

    public static function fromClass(string $className): self
    {
        return match ($className) {
            \App\Models\Subscription::class => self::SUBSCRIPTION,
            \App\Models\Payment::class => self::PAYMENT,
        };
    }
}