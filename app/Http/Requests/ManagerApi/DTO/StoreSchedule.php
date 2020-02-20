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
    /**
     * @var string|null
     */
    public ?string $branch_id;

    /**
     * @var string|null
     */
    public ?string $classroom_id;

    /**
     * @var string|null
     */
    public ?string $course_id;

    /**
     * @var string
     */
    public string $weekday;

    /**
     * @var \Carbon\Carbon|null
     */
    public ?\Carbon\Carbon $starts_at;

    /**
     * @var \Carbon\Carbon|null
     */
    public ?\Carbon\Carbon $ends_at;

    public User $user;
}
