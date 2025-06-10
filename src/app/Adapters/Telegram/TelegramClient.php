<?php

namespace App\Adapters\Telegram;

use App\Adapters\Client;
use App\Common\Locator;
use Illuminate\Support\Facades\Http;

class TelegramClient extends Client
{

    protected Transport\TelegramAdapterTransport $transport;

    public function __construct(\App\Adapters\Telegram\Transport\TelegramAdapterTransport $transport)
    {
        $this->transport = $transport;
    }

    public static function withTransport(string $transportType): self
    {
        $transport = match ($transportType) {
            'http' => \App\Adapters\Telegram\Transport\TelegramAdapterHttp::class,
            'queue' => \App\Adapters\Telegram\Transport\TelegramAdapterQueue::class,
            default => throw new TelegramClientException("Invalid transport")
        };

        return new self(new $transport());
    }

    public function externalSystemName(): string
    {
        return 'telegram';
    }

    public function ping(): bool
    {
        try {
            return $this->transport->ping();
        } catch (\Exception $e) {
            throw new TelegramClientException('Telegram server is not responding: ' .$e->getMessage(), [], 500);
        }
    }

    public function sendMessage(string $phone, string $message): void
    {
        try {
            $this->transport->sendMessage($phone, $message);
        } catch (\Exception $e) {
            throw new TelegramClientException('Message not sent: '. $e->getMessage(), [], 500);
        }
    }

}