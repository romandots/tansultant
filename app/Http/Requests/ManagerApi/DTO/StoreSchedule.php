<?php
/**
 * File: Schedule.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

use App\Models\User;

/**
 * Class Schedule
 * @package App\Http\Requests\ManagerApi\DTO
 */
class StoreSchedule
{
    public ?string $branch_id;

    public ?string $classroom_id;

    public ?string $course_id;

    public string $cycle;

    public ?string $weekday;

    public ?\Carbon\Carbon $from_date;

    public ?\Carbon\Carbon $to_date;

    public ?\Carbon\Carbon $starts_at;

    public ?\Carbon\Carbon $ends_at;

    public User $user;
}
