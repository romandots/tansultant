<?php
/**
 * File: VerificationCodeExpired.php
 * Author: Roman Dots <romandots@brainex.co>
 * Date: 2020-2-19
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Services\Verify\Exceptions;

use App\Exceptions\BaseException;

/**
 * Class VerificationCodeIsInvalid
 * @package App\Services\Verify\Exceptions
 */
class VerificationCodeExpired extends BaseException
{
    /**
     * VerificationCodeIsInvalid constructor.
     */
    public function __construct()
    {
        parent::__construct('verification_code_expired', null, 404);
    }
}
