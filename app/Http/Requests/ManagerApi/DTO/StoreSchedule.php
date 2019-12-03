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
     * @var string
     */
    public $course_id;

    /**
     * @var \Carbon\Carbon|null
     */
    public $starts_at;

    /**
     * @var \Carbon\Carbon|null
     */
    public $ends_at;

    /**
     * @var int
     */
    public $duration;

    /**
     * @var \Carbon\Carbon|null
     */
    public $monday;

    /**
     * @var \Carbon\Carbon|null
     */
    public $tuesday;

    /**
     * @var \Carbon\Carbon|null
     */
    public $wednesday;

    /**
     * @var \Carbon\Carbon|null
     */
    public $thursday;

    /**
     * @var \Carbon\Carbon|null
     */
    public $friday;

    /**
     * @var \Carbon\Carbon|null
     */
    public $saturday;

    /**
     * @var \Carbon\Carbon|null
     */
    public $sunday;
}
