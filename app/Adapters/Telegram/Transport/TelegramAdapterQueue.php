<?php

namespace App\Adapters\Telegram\Transport;

use App\Adapters\Telegram\TelegramClientException;
use App\Jobs\Telegram\PingJobProducer;
use Illuminate\Support\Facades\Http;
use Girni\LaravelRabbitMQ\Message\BaseMessage;

class TelegramAdapterQueue implements TelegramAdapterTransport
{

    public string $queue;

    public function __construct()
    {
        $this->queue = config('telegram.queue');

        if (!$this->queue) {
            throw new \Exception('Telegram queue is not set');
        }
    }
    
    public function ping(): bool
    {
        try {
            $message = BaseMessage::fromArray([
                'action' => 'ping',
            ]);
            $job = new PingJobProducer($message);
            dispatch($job)->onQueue($this->queue);

            return true;
        } catch (\Exception $e) {
            throw new TelegramClientException('Telegram job producer error: ' .$e->getMessage(), [], 500);
        }
    }

    public function sendMessage(string $phone, string $message): void
    {
        try {
            throw new \RuntimeException("Not implemented");
        } catch (\Exception $e) {
            throw new TelegramClientException('Message not sent: '. $e->getMessage(), [], 500);
        }
    }
}