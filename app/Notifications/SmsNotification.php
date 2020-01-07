<?php
/**
 * File: SmsNotification.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-8
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Nutnet\LaravelSms\Notifications\NutnetSmsChannel;
use Nutnet\LaravelSms\Notifications\NutnetSmsMessage;

/**
 * Class SmsNotification
 * @package App\Notifications
 * @property string $message
 */
class SmsNotification extends Notification
{
    use Queueable;

    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return [NutnetSmsChannel::class];
    }

    public function toSms($notifiable): NutnetSmsMessage
    {
        return new NutnetSmsMessage($this->message);
    }
}
