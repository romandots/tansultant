<?php

namespace App\Services\Notification;

enum NotificationChannel: string
{
    case SMS = 'sms';
    case EMAIL = 'email';
    case PUSH = 'push';
}