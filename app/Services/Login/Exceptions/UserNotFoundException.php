<?php
/**
 * File: UserNotFoundException.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Login\Exceptions;

use App\Exceptions\BaseException;

/**
 * Class UserNotFoundException
 * @package App\Services\Login\Exceptions
 */
class UserNotFoundException extends BaseException
{
    /**
     * UserNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct('user_not_found', null, 404);
    }
}
