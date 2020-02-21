<?php
/**
 * File: ScheduleExists.php
 * Author: Roman Dots <romandots@brainex.co>
 * Date: 2020-2-21
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Repository\Exceptions;

use App\Exceptions\BaseException;
use App\Models\Schedule;

class ScheduleSlotIsOccupied extends BaseException
{
    /**
     * @var Schedule[]
     */
    private array $schedules;

    /**
     * ScheduleSlotIsOccupied constructor.
     * @param Schedule[] $schedules
     */
    public function __construct(array $schedules)
    {
        parent::__construct('schedule_slot_is_occupied', $schedules, 409);
        $this->schedules = $schedules;
    }

    /**
     * @return Schedule[]
     */
    public function getSchedules(): array
    {
        return $this->schedules;
    }
}