<?php
/**
 * File: Schedule.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

/**
 * Class Schedule
 * @package App\Http\Requests\ManagerApi\DTO
 */
class StoreSchedule
{
    /**
     * @var string
     */
    public $branch_id;

    /**
     * @var string
     */
    public $classroom_id;

    /**
     * @var string|null
     */
    public $course_id;

    /**
     * @var string
     */
    public $weekday;

    /**
     * @var \Carbon\Carbon|null
     */
    public $starts_at;

    /**
     * @var \Carbon\Carbon|null
     */
    public $ends_at;
}
