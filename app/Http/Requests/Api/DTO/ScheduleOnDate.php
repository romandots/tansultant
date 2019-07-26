<?php
/**
 * File: ScheduleOnDate.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-25
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\Api\DTO;

/**
 * Class ScheduleOnDate
 * @package App\Http\Requests\Api\DTO
 */
class ScheduleOnDate
{
    /**
     * @var int|null
     */
    public $branch_id;

    /**
     * @var int|null
     */
    public $classroom_id;

    /**
     * @var int|null
     */
    public $course_id;

    /**
     * @var \Carbon\Carbon
     */
    public $date;
}
