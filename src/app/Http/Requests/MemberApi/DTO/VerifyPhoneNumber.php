<?php
/**
 * File: VerifyPhoneNumber.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-8
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Http\Requests\MemberApi\DTO;

class VerifyPhoneNumber
{
    public string $phone;

    public ?string $verification_code = null;
}
