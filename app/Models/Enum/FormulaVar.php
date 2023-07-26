<?php

namespace App\Models\Enum;

enum FormulaVar: string
{
    case STUDENT = 'С';
    case ACTIVE_STUDENT = 'АС';
    case SUBSCRIPTION = 'П';
    case ACTIVE_SUBSCRIPTION = 'АП';
    case VISIT = 'ВП';
    case PAID_VISIT = 'ПП';
    case FREE_VISIT = 'БП';
    case HOUR = 'Ч';
    case MINUTE = 'М';

    public static function names(): array
    {
        return array_map(static fn(self $case) => $case->name, self::cases());
    }

    public static function values(): array
    {
        return array_map(static fn(self $case) => $case->value, self::cases());
    }

    public static function descriptions(): array
    {
        return array_map(static fn (self $case) => $case->getDescription(), self::cases());
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::STUDENT => 'Студенты',
            self::ACTIVE_STUDENT => 'Активные студенты',
            self::SUBSCRIPTION => 'Подписки',
            self::ACTIVE_SUBSCRIPTION => 'Активные подписки',
            self::VISIT => 'Все посещения',
            self::PAID_VISIT => 'Платные посещения',
            self::FREE_VISIT => 'Бесплатные посещения',
            self::HOUR => 'Часы',
            self::MINUTE => 'Минуты',
            default => '',
        };
    }
}