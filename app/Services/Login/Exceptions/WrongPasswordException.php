<?php
/**
 * File: LoginFailedException.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Login\Exceptions;

use App\Exceptions\BaseException;

/**
 * Class LoginFailedException
 * @package App\Services\Login\Exceptions
 */
class WrongPasswordException extends BaseException
{
    /**
     * LoginFailedException constructor.
     */
    public function __construct()
    {
        parent::__construct('wrong_password', null, 401);
    }
}
