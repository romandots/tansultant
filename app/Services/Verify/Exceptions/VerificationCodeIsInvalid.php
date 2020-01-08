<?php
/**
 * File: VerificationCodeIsInvalid.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Verify\Exceptions;

use App\Exceptions\BaseException;

/**
 * Class VerificationCodeIsInvalid
 * @package App\Services\Verify\Exceptions
 */
class VerificationCodeIsInvalid extends BaseException
{
    /**
     * VerificationCodeIsInvalid constructor.
     */
    public function __construct()
    {
        parent::__construct('verification_code_is_invalid', null, 409);
    }
}
