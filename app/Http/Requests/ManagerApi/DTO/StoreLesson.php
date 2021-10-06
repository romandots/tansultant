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
    public string $name;
    public ?string $course_id;
    public ?string $instructor_id;
    public string $branch_id;
    public string $classroom_id;
    public ?string $schedule_id;
    public string $type;
    public \Carbon\Carbon $starts_at;
    public \Carbon\Carbon $ends_at;
}
