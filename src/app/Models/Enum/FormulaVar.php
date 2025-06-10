<?php

namespace App\Models\Enum;

enum FormulaVar: string
{
    case ALL_VISITS = 'ВП';
    case VISITS_BY_SUBSCRIPTION = 'ПП';
    case VISITS_BY_PAYMENT = 'БП';
    case ACTIVE_STUDENTS = 'АС';
    case INACTIVE_STUDENTS = 'НС';
    case ACTIVE_SUBSCRIPTIONS = 'АП';
    case HOUR = 'Ч';
    case MINUTE = 'М';

    public function getDescription(): string
    {
        return match ($this) {
            self::ALL_VISITS => 'Все посещения',
            self::VISITS_BY_SUBSCRIPTION => 'Посещения по подписке',
            self::VISITS_BY_PAYMENT => 'Посещения без подписки',
            self::ACTIVE_STUDENTS => '«Активные» студенты',
            self::INACTIVE_STUDENTS => 'Не «Активные» студенты',
            self::ACTIVE_SUBSCRIPTIONS => '«Активные» подписки',
            self::HOUR => 'Часы',
            self::MINUTE => 'Минуты',
            default => '',
        };
    }

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
}