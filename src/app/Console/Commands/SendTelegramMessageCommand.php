<?php

namespace App\Console\Commands;

use App\Adapters\Telegram\TelegramClient;
use App\Common\Locator;
use Illuminate\Console\Command;

class SendTelegramMessageCommand extends Command
{
    protected $signature = 'telegram:message {phone} {message} {transport?}';
    protected $description = 'Send message over Telegram via Telegram Client';

    public function handle(): void
    {
        $telegramClient = $this->getTelegramClientWithTransport();

        $messageSent = $telegramClient->sendMessage($this->argument('phone'), $this->argument('message'));
        if ($messageSent) {
            $this->info('Message sent!');
        } else {
            $this->error('Message not sent');
        }
    }

    protected function getTelegramClientWithTransport(): TelegramClient
    {
        return match ($this->argument('transport')) {
            'http' => TelegramClient::withTransport('http'),
            'queue' => TelegramClient::withTransport('queue'),
            default => Locator::get(TelegramClient::class),
        };
    }
}
