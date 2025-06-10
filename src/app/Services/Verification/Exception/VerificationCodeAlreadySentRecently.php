<?php
/**
 * File: VerificationCodeAlreadySentRecently.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Verification\Exception;

use App\Exceptions\BaseException;

/**
 * Class VerificationCodeAlreadySentRecently
 * @package App\Services\Verify\Exceptions
 */
class VerificationCodeAlreadySentRecently extends BaseException
{
    /**
     * VerificationCodeAlreadySentRecently constructor.
     * @param $timeout
     */
    public function __construct($timeout)
    {
        parent::__construct(
            'verification_code_was_sent_recently',
            ['timeout' => $timeout],
            400
        );
    }
}
