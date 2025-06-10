<?php

namespace App\Events\Schedule;

use Carbon\Carbon;

class ScheduleUpdatedEvent extends \App\Events\BaseEvent
{

    public function __construct(
        public Carbon $date,
        public string $branchId,
    ) {
    }

    public function getChannelName(): string
    {
        return \sprintf('schedule.%s.%s', $this->getDate()->format('Y-m-d'), $this->getBranchId());
    }

    /**
     * @return Carbon
     */
    public function getDate(): Carbon
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getBranchId(): string
    {
        return $this->branchId;
    }
}