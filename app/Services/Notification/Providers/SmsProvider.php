<?php

namespace App\Services\Notification\Providers;

use Nutnet\LaravelSms\SmsSender;

class SmsProvider extends Provider
{
    public function __construct(
        public readonly SmsSender $sender,
    ) { }

    public function send(string $message): void
    {
        if (empty($this->recipients)) {
            throw new \Exception('Recipient is not set');
        }

        if (count($this->recipients) === 1) {
            $this->sender->send($this->recipients[0], $message, $this->options);
            return;
        }

        $this->sender->sendBatch($this->recipients, $message, $this->options);
    }
}