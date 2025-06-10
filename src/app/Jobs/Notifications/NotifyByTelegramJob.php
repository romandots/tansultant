<?php

namespace App\Jobs\Notifications;

use App\Services\Notification\Facade;

class NotifyByTelegramJob extends NotificationJob
{
    public function handle(Facade $notifications): void
    {
        $notifications->telegram($this->person, $this->message);
        $this->getLogger()->info('Notification job finished: message sent to telegram');
    }
}