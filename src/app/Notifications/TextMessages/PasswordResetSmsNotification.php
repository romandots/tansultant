<?php
/**
 * File: PasswordResetSmsNotification.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-9
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Notifications\TextMessages;

use App\Notifications\SmsNotification;

class PasswordResetSmsNotification extends SmsNotification
{
    public function __construct(string $password)
    {
        parent::__construct(\trans('password_reset.new_password_text_message', ['new_password' => $password]));
    }
}
