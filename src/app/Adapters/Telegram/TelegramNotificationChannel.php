<?php

namespace App\Adapters\Telegram;

use App\Models\Person;
use App\Notifications\TelegramNotification;
use Illuminate\Notifications\Notification;

class TelegramNotificationChannel extends TelegramClient
{
    /**
     * Send the given notification.
     *
     * @param Person $notifiable
     * @param TelegramNotification $notification
     */
    public function send(object $notifiable, Notification $notification): void
    {
        $message = $notification->getMessage();
        $this->sendMessage($notifiable->telegram_username ?? $notifiable->phone, $message);
    }
}