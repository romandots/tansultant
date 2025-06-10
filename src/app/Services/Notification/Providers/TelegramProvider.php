<?php

namespace App\Services\Notification\Providers;


use App\Adapters\Telegram\TelegramClient;
use App\Common\Traits\WithLogger;

class TelegramProvider extends Provider
{
    use WithLogger;

    public function __construct(
        public readonly TelegramClient $sender,
    ) { }

    public function send(string $message): void
    {
        if (empty($this->recipients)) {
            throw new \Exception('Recipient is not set');
        }

        foreach ($this->recipients as $recipient) {
            $this->sender->sendMessage($recipient, $message);
            $this->getLogger()->info('Message sent to ' . $recipient . ' via Telegram: ' . $message);
        }
    }
}