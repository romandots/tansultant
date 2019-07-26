<?php
/**
 * File: CreatesFakeSchedule.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Schedule;

/**
 * Class CreatesFakeSchedule
 * @package Tests\Traits
 */
trait CreatesFakeSchedule
{
    /**
     * @param array $attributes
     * @return Schedule
     */
    private function createFakeSchedule(array $attributes = []): Schedule
    {
        return \factory(Schedule::class)->create($attributes);
    }
}
