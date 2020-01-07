<?php
/**
 * File: TextMessageService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-8
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Services\TextMessaging;

use Nutnet\LaravelSms\SmsSender;

class TextMessaging implements TextMessagingServiceInterface
{
    private SmsSender $sender;

    /**
     * TextMessageService constructor.
     * @param SmsSender $sender
     */
    public function __construct(SmsSender $sender)
    {
        $this->sender = $sender;
    }

    /**
     * @param $phoneNumber
     * @param string $message
     * @param array|null $options
     */
    public function send($phoneNumber, string $message, ?array $options = []): void
    {
        $this->sender->send($phoneNumber, $message, $options);
    }
}
