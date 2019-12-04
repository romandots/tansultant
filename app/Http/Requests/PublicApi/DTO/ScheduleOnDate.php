<?php
/**
 * File: ScheduleOnDate.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\PublicApi\DTO;

/**
 * Class ScheduleOnDate
 * @package App\Http\Requests\ManagerApi\DTO
 */
class ScheduleOnDate
{
    /**
     * @var string|null
     */
    public $branch_id;

    /**
     * @var string|null
     */
    public $classroom_id;

    /**
     * @var string|null
     */
    public $course_id;

    /**
     * @var \Carbon\Carbon
     */
    public $date;

    /**
     * @var \Carbon\Carbon
     */
    public $weekday;
}
