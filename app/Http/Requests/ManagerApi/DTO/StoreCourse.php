<?php
/**
 * File: Course.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

use App\Models\Instructor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

/**
 * Class Course
 * @package App\Http\Requests\ManagerApi\DTO
 */
class StoreCourse
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var string|null
     */
    public ?string $summary;

    /**
     * @var string|null
     */
    public ?string $description;

    /**
     * @var bool
     */
    public bool $display;

    /**
     * Array structure is:
     *  [
     *      'from' => (int|null),
     *      'to' => (int|null)
     *  ]
     * @var int[]
     */
    public array $age_restrictions;

    /**
     * @var UploadedFile|null
     */
    public ?UploadedFile $picture;

    /**
     * @var string
     */
    public string $status;

    /**
     * @var Instructor|null
     */
    public ?Instructor $instructor;

    /**
     * @var Carbon|null
     */
    public ?Carbon $starts_at;

    /**
     * @var Carbon|null
     */
    public ?Carbon $ends_at;

    /**
     * @var User
     */
    public User $user;
}
