<?php
/**
 * File: TextMessageSendingFailed.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Verification\Exception;

use App\Exceptions\BaseException;

/**
 * Class TextMessageSendingFailed
 * @package App\Services\Verify\Exceptions
 */
class TextMessageSendingFailed extends BaseException
{
    /**
     * TextMessageSendingFailed constructor.
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct('text_message_sending_failed', ['message' => $message], 400);
    }
}
