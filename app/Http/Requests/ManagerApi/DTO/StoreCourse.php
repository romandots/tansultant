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
    public string $name;

    /**
     * @var string|null
     */
    public ?string $summary = null;

    /**
     * @var string|null
     */
    public ?string $description = null;

    /**
     * @var int|null
     */
    public ?int $age_restrictions_from = null;

    /**
     * @var int|null
     */
    public ?int $age_restrictions_to = null;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public ?\Illuminate\Http\UploadedFile $picture = null;

    /**
     * @var string
     */
    public string $status;

    /**
     * @var string|null
     */
    public ?string $instructor_id = null;

    /**
     * @var \Carbon\Carbon|null
     */
    public ?\Carbon\Carbon $starts_at = null;

    /**
     * @var \Carbon\Carbon|null
     */
    public ?\Carbon\Carbon $ends_at = null;

    /**
     * @var string[]
     */
    public array $genres = [];
}
