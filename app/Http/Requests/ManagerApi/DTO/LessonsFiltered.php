<?php
/**
 * File: LessonsOnDate.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

/**
 * Class LessonsOnDate
 * @package App\Http\Requests\ManagerApi\DTO
 */
class LessonsFiltered
{
    /**
     * @var \Carbon\Carbon
     */
    public \Carbon\Carbon $date;

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
}
