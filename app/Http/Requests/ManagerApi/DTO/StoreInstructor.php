<?php
/**
 * File: StoreInstructor.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

/**
 * Class StoreInstructor
 * @package App\Http\Requests\ManagerApi\DTO
 */
class StoreInstructor
{
    public ?string $name;
    public ?string $description;
    public string $status;
    public bool $display;
}
