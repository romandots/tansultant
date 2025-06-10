<?php

declare(strict_types=1);

namespace App\Services\UserRegister\Exceptions;

use App\Exceptions\BaseException;

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
