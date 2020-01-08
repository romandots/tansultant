<?php
/**
 * File: VeryNotifiable.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-8
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Models\Traits;

/**
 * Trait Notifiable
 * @package App\Models
 * @property-read string $phone
 */
trait Notifiable
{
    use \Illuminate\Notifications\Notifiable;

    /**
     * Phone number for SMS notifications
     * @return string
     */
    public function routeNotificationForNutnetSms(): string {
        return $this->phone;
    }
}
