<?php
/**
 * File: TextMessageServiceInterface.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-8
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Services\TextMessaging;

interface TextMessagingServiceInterface
{
    public function send($phoneNumber, string $message, ?array $options = []): void;
}
