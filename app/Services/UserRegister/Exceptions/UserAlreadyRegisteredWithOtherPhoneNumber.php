<?php
/**
 * File: UserAlreadyRegisteredWithOtherPhone.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\UserRegister\Exceptions;

use App\Exceptions\BaseException;

/**
 * Class UserAlreadyRegisteredWithOtherPhone
 * @package App\Services\UserRegister\Exceptions
 */
class UserAlreadyRegisteredWithOtherPhoneNumber extends BaseException
{
    /**
     * UserAlreadyRegisteredWithOtherPhone constructor.
     */
    public function __construct()
    {
        parent::__construct('user_already_registered_with_another_phone_number', null, 409);
    }
}
