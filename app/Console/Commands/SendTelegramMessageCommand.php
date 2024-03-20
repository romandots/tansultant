<?php

namespace App\Console\Commands;

use App\Adapters\Telegram\TelegramClient;
use Illuminate\Console\Command;

class SendTelegramMessageCommand extends Command
{
    protected $signature = 'message:telegram {phone} {message}';
    protected $description = 'Send message over Telegram via Telegram Client';

    public function handle(TelegramClient $telegramClient): void
    {
        $messageSent = $telegramClient->sendMessage($this->argument('phone'), $this->argument('message'));
        if ($messageSent) {
            $this->info('Message sent!');
        } else {
            $this->error('Message not sent. Check the phone number.');
        }
    }
}
