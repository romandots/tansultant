<?php

namespace App\Broadcasting;

use Carbon\Carbon;

class ScheduleChannel
{
    public function join(\App\Models\User $user, Carbon $date, string $branchId): bool
    {
        return true;
    }
}