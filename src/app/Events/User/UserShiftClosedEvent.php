<?php

declare(strict_types=1);

namespace App\Events\User;

class UserShiftClosedEvent extends UserEvent
{
    public function __construct(public readonly \App\Models\Shift $shift)
    {
        parent::__construct($this->shift->user);
    }

    /**
     * @return \App\Models\Shift
     */
    public function getShift(): \App\Models\Shift
    {
        return $this->shift;
    }
}
