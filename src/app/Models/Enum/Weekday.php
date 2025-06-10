<?php

namespace App\Models\Enum;

enum Weekday: int
{
    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;
    case SUNDAY = 7;

    public function trans(): string
    {
        return \trans('schedule.weekday.' . $this->value);
    }
}