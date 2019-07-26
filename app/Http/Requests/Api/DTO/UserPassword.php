<?php
/**
 * File: UserPassword.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-21
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\Api\DTO;

/**
 * Class UserPassword
 * @package App\Http\Requests\Api\DTO
 */
class UserPassword
{
    /**
     * @var string
     */
    public $old_password;

    /**
     * @var string
     */
    public $new_password;
}
