<?php

namespace App\Services\Notification;

use App\Models\Person;

class Facade
{
    public function getService(): NotificationService
    {
        return app(NotificationService::class);
    }

    public function notify(Person $person, string $message): void
    {
        $this->getService()->notify($person, $message);
    }

    public function sms(Person $person, string $message): void
    {
        $this->getService()->notifyByChannel($person, NotificationChannel::SMS, $message);
    }

    public function email(Person $person, string $message): void
    {
        $this->getService()->notifyByChannel($person, NotificationChannel::EMAIL, $message);
    }

    public function push(Person $person, string $message): void
    {
        $this->getService()->notifyByChannel($person, NotificationChannel::PUSH, $message);
    }

    public function sendPasswordResetNotification(\App\Models\User $user, string $password): void
    {
        $this->notify(
            $user->person,
            \trans('password_reset.new_password_text_message', ['new_password' => $password])
        );
    }

}