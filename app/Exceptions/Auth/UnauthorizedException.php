<?php
/**
 * File: UnauthorizedException.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Exceptions\Auth;

use App\Exceptions\BaseException;

/**
 * Class UnauthorizedException
 */
class UnauthorizedException extends BaseException
{
    /**
     * UnauthorizedException constructor.
     */
    public function __construct()
    {
        parent::__construct('unauthorized', null, 401);
    }
}
