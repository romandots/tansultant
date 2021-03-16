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
    public string $name;
    public ?string $summary = null;
    public ?string $description = null;
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

    public ?\Illuminate\Http\UploadedFile $picture = null;

    /**
     * @var string
     */
    public string $status;

    public ?Instructor $instructor;

    public ?\Carbon\Carbon $starts_at = null;

    public ?\Carbon\Carbon $ends_at = null;

    /**
     * @var string[]
     */
    public array $genres = [];

    public User $user;
}
