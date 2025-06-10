<?php

namespace App\Adapters\Telegram\Transport;

interface TelegramAdapterTransport
{
    public function ping(): bool;
    public function sendMessage(string $phone, string $message): void;
}