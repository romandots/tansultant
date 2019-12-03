<?php
/**
 * File: Course.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

/**
 * Class Course
 * @package App\Http\Requests\ManagerApi\DTO
 */
class StoreCourse
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string|null
     */
    public $summary;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @var string|null
     */
    public $age_restrictions;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $picture;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string|null
     */
    public $instructor_id;

    /**
     * @var \Carbon\Carbon|null
     */
    public $starts_at;

    /**
     * @var \Carbon\Carbon|null
     */
    public $ends_at;
}
