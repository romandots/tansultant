<?php
/**
 * File: ScheduleOnDate.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

/**
 * Class ScheduleOnDate
 * @package App\Http\Requests\ManagerApi\DTO
 */
class ScheduleOnDate
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
     * @var \Carbon\Carbon
     */
    public \Carbon\Carbon $date;

    /**
     * @var int
     */
    public int $weekday;
}
