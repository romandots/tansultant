<?php
/**
 * File: Lesson.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

/**
 * Class Lesson
 * @package App\Http\Requests\ManagerApi\DTO
 */
class StoreLesson
{
    /**
     * @var string|null
     */
    public $course_id;

    /**
     * @var string|null
     */
    public $instructor_id;

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
