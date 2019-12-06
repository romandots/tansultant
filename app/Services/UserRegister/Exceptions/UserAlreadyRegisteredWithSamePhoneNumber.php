<?php
/**
 * File: UserAlreadyRegistered.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\UserRegister\Exceptions;

use App\Exceptions\BaseException;

/**
 * Class UserAlreadyRegistered
 * @package App\Services\UserRegister\Exceptions
 */
class UserAlreadyRegisteredWithSamePhoneNumber extends BaseException
{
    /**
     * UserAlreadyRegistered constructor.
     */
    public function __construct()
    {
        parent::__construct('user_with_this_phone_number_already_registered', null, 409);
    }
}
