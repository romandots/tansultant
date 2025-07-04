<?php

namespace App\Models\Enum;

enum ScheduleCycle: string
{
    case ONCE = 'once';
    case EVERY_MONTH = 'month';
    case EVERY_WEEK = 'week';
    case EVERY_DAY = 'day';

    public function trans(array $subs = []): string
    {
        return \trans('schedule.cycle.' . $this->value, $subs);
    }
}