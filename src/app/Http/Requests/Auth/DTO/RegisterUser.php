<?php
/**
 * File: RegisterUser.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\Auth\DTO;

use App\Models\Enum\Gender;
use App\Models\Enum\UserType;

class RegisterUser
{
    public string $last_name;

    public string $first_name;

    public string $patronymic_name;

    public \Carbon\Carbon $birth_date;

    public Gender $gender;

    public ?string $phone;

    public ?string $email;

    /**
     * For instructors
     *
     * @var string|null
     */
    public ?string $description = null;

    public UserType $user_type;

    public string $password;

    public ?string $verification_code;
}
