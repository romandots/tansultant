<?php
/**
 * File: UserUpdate.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-21
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi\DTO;

/**
 * Class UserUpdate
 * @package App\Http\Requests\ManagerApi\DTO
 */
class UpdateUser
{
    public ?string $name = null;

    public ?string $username = null;

    public ?string $password = null;
}
