<?php
/**
 * File: StoreInstructor.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-8
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Http\Requests\DTO;

/**
 * Class StoreInstructor
 * @package App\Http\Requests\ManagerApi\DTO
 */
class StoreInstructor
{
    public ?string $name;
    public ?string $description = null;
    public string $status;
    public bool $display;
}
