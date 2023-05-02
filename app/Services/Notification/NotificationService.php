<?php

namespace App\Services\Notification;

use App\Common\BaseService;
use App\Models\Person;
use App\Services\Notification\Providers\Provider;
use App\Services\Notification\Providers\SmsProvider;

class NotificationService extends BaseService
{
    public function sms(Person $person, string $message): void
    {
        $this->notifyByChannel($person, NotificationChannel::SMS, $message);
    }

    public function email(Person $person, string $message): void
    {
        $this->notifyByChannel($person, NotificationChannel::EMAIL, $message);
    }

    public function push(Person $person, string $message): void
    {
        $this->notifyByChannel($person, NotificationChannel::PUSH, $message);
    }

    public function notify(Person $person, string $message): void
    {
        $channels = $this->getPersonChannels($person);
        foreach ($channels as $channel) {
            $this->notifyByChannel($person, $channel, $message);
        }
    }

    protected function notifyByChannel(Person $person, NotificationChannel $channel, string $message): void
    {
        $this->debug(
            'Sending notification over ' . $channel->value,
            ['person' => $person->name, 'message' => $message]
        );
        $sender = $this->getProvider($channel);
        $recipient = $this->getRecipientByChannel($person, $channel);
        $sender->to($recipient)->send($message);
    }

    protected function getRecipientByChannel(Person $person, NotificationChannel $channel): string
    {
        $recipient = match ($channel) {
            NotificationChannel::SMS => $person->phone,
            NotificationChannel::EMAIL => $person->email,
            NotificationChannel::PUSH => null,//$person->push_token,
        };

        if (empty($recipient)) {
            throw new \RuntimeException('Recipient has no contacts for ' . $channel->value . ' channel');
        }

        return $recipient;
    }

    protected function getProvider(NotificationChannel $channel): Provider
    {
        return match ($channel) {
            NotificationChannel::SMS => app(SmsProvider::class),
            NotificationChannel::EMAIL => throw new \Exception('Email provider is not implemented'),
            NotificationChannel::PUSH => throw new \Exception('Push provider is not implemented'),
        };
    }

    protected function getPersonChannels(Person $person): array
    {
        return [
            NotificationChannel::SMS,
        ];
    }
}