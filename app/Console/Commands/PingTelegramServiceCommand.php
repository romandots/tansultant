<?php

namespace App\Console\Commands;

use App\Adapters\Telegram\TelegramClient;
use Illuminate\Console\Command;

class PingTelegramServiceCommand extends Command
{
    protected $signature = 'ping:telegram';
    protected $description = 'Ping Telegram server';

    public function handle(TelegramClient $telegramClient): void
    {
        if ($telegramClient->ping()) {
            $this->info('Server responding!');
        } else {
            $this->error('Server not responding.');
        }
    }
}
