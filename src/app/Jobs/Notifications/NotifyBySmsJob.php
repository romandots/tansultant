<?php

namespace App\Jobs\Notifications;

use App\Services\Notification\Facade;

class NotifyBySmsJob extends NotificationJob
{
    public function handle(Facade $notifications): void
    {
        $notifications->sms($this->person, $this->message);
        $this->getLogger()->info('Notification job finished: message sent by SMS');
    }
}