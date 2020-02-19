<?php
/**
 * File: OldPasswordInvalidException.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-21
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\User\Exceptions;

/**
 * Class OldPasswordInvalidException
 * @package App\Services\User\Exceptions
 */
class OldPasswordInvalidException extends UserServiceException
{
    /**
     * OldPasswordInvalidException constructor.
     */
    public function __construct()
    {
        parent::__construct('old_password_invalid', null, 409);
    }
}
