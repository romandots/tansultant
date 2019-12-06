<?php
/**
 * File: VerificationCodeWasSentTooManyTimes.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Verify\Exceptions;

use App\Exceptions\BaseException;

/**
 * Class VerificationCodeWasSentTooManyTimes
 * @package App\Services\Verify\Exceptions
 */
class VerificationCodeWasSentTooManyTimes extends BaseException
{
    /**
     * VerificationCodeWasSentTooManyTimes constructor.
     * @param $maxTries
     */
    public function __construct($maxTries)
    {
        parent::__construct(
            'verification_code_was_sent_too_many_times',
            ['max_tries' => $maxTries],
            400
        );
    }
}
