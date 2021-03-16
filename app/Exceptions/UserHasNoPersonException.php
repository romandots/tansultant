<?php
/**
 * File: UserHasNoPersonException.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-12
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Exceptions;

class UserHasNoPersonException extends BaseException
{
    public function __construct()
    {
        parent::__construct('user_has_no_person_attached', null, 403);
    }
}
