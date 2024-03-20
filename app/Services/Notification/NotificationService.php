<?php

namespace App\Services\Notification;

use App\Common\BaseService;
use App\Jobs\Notifications\NotificationJob;
use App\Models\Person;
use App\Services\Notification\Providers\Provider;
use App\Services\Notification\Providers\SmsProvider;
use App\Services\Notification\Providers\TelegramProvider;

class NotificationService extends BaseService
{
    public function notify(Person $person, string $message): void
    {
        $channels = $this->getPersonChannels($person);
        foreach ($channels as $channel) {
            match ($channel) {
                NotificationChannel::SMS => NotificationJob::sms($person, $message),
                NotificationChannel::TELEGRAM => NotificationJob::telegram($person, $message),
                NotificationChannel::EMAIL => throw new \Exception('Email provider is not implemented'),
                NotificationChannel::PUSH => throw new \Exception('Push provider is not implemented'),
            };
        }
    }

    public function notifyByChannel(Person $person, NotificationChannel $channel, string $message): void
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
            NotificationChannel::TELEGRAM => $person->telegram_username ?? $person->phone,
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
            NotificationChannel::TELEGRAM => app(TelegramProvider::class),
            NotificationChannel::EMAIL => throw new \Exception('Email provider is not implemented'),
            NotificationChannel::PUSH => throw new \Exception('Push provider is not implemented'),
        };
    }

    protected function getPersonChannels(Person $person): array
    {
        return [
            NotificationChannel::TELEGRAM,
            NotificationChannel::SMS,
        ];
    }
}