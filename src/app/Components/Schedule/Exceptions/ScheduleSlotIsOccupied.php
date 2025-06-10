<?php
/**
 * File: ScheduleExists.php
 * Author: Roman Dots <romandots@brainex.co>
 * Date: 2020-2-21
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Components\Schedule\Exceptions;

class ScheduleSlotIsOccupied extends Exception
{
    /**
     * @var array<\App\Models\Schedule>
     */
    private array $schedules;

    /**
     * ScheduleSlotIsOccupied constructor.
     * @param array<\App\Models\Schedule> $schedules
     */
    public function __construct(array $schedules)
    {
        parent::__construct('schedule_slot_is_occupied', $schedules, 409);
        $this->schedules = $schedules;
    }

    /**
     * @return array<\App\Models\Schedule>
     */
    public function getSchedules(): array
    {
        return $this->schedules;
    }
}