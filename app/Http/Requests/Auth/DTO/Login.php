<?php
/**
 * File: Login.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\Auth\DTO;

/**
 * Class Login
 * @package App\Http\Requests\Auth\DTO
 */
class Login
{
    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;
}
