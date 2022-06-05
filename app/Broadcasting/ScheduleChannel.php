<?php

namespace App\Broadcasting;

use Carbon\Carbon;

class ScheduleChannel
{
    public function join(\App\Models\User $user, string $classroomId, Carbon $date): bool
    {
        return true;
    }
}