<?php
/**
 * File: $fileName
 * Author: Roman Dots <romandots@brainex.co>
 * Date: 2020-2-19
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Services\PasswordReset\Exceptions;

use App\Exceptions\BaseException;

class UserHasNoPerson extends BaseException
{
    /**
     * UserHasNoPerson constructor.
     */
    public function __construct()
    {
        parent::__construct('user_has_no_person', null, 404);
    }
}