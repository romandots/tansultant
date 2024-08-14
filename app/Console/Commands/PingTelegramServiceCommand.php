<?php

namespace App\Console\Commands;

use App\Adapters\Telegram\TelegramClient;
use App\Common\Locator;
use Illuminate\Console\Command;

class PingTelegramServiceCommand extends Command
{
    protected $signature = 'telegram:ping {transport?}';
    protected $description = 'Ping Telegram server';

    public function handle(): void
    {
        $telegramClient = $this->getTelegramClientWithTransport();

        if ($telegramClient->ping()) {
            $this->info('Server responding!');
        } else {
            $this->error('Server not responding.');
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
