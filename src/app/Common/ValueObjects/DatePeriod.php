<?php

namespace App\Common\ValueObjects;

use Carbon\Carbon;

class DatePeriod
{
    public Carbon $start;
    public Carbon $end;

    public function __construct(Carbon $start, Carbon|int $end)
    {
        $this->start = $start;
        $this->end = $end instanceof Carbon
            ? $end
            : $start->clone()->addMinutes($end);
    }
}