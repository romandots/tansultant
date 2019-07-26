<?php
/**
 * File: Lesson.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\Api\DTO;

/**
 * Class Lesson
 * @package App\Http\Requests\Api\DTO
 */
class Lesson
{
    /**
     * @var int|null
     */
    public $course_id;

    /**
     * @var int|null
     */
    public $instructor_id;

    /**
     * @var int
     */
    public $branch_id;

    /**
     * @var int
     */
    public $classroom_id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var \Carbon\Carbon
     */
    public $starts_at;

    /**
     * @var \Carbon\Carbon
     */
    public $ends_at;
}
