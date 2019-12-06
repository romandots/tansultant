<?php
/**
 * File: RegisterUser.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\DTO;

/**
 * Class RegisterUser
 * @package App\Http\Requests\DTO
 */
class RegisterUser
{
    public const TYPE_INSTRUCTOR = 'instructor';
    public const TYPE_STUDENT = 'student';

    /**
     * @var string|null
     */
    public $last_name;

    /**
     * @var string|null
     */
    public $first_name;

    /**
     * @var string|null
     */
    public $patronymic_name;

    /**
     * @var \Carbon\Carbon
     */
    public $birth_date;

    /**
     * @var string|null
     */
    public $gender;

    /**
     * @var string|null
     */
    public $phone;

    /**
     * @var string|null
     */
    public $email;

    /**
     * For instructors
     *
     * @var string|null
     */
    public $description;

    /**
     * @var string
     */
    public $user_type;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string|null
     */
    public $confirmation_code;

    /**
     * @var \Carbon\Carbon|null
     */
    public $verified_at;

    private $sex;
}
