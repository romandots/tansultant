<?php
/**
 * File: ResetPassword.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-9
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Http\Requests\Auth\DTO;

class ResetPassword
{
    public string $username;

    public ?string $verification_code = null;
}
