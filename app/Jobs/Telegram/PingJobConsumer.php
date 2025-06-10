<?php

namespace App\Jobs\Telegram;

use Girni\LaravelRabbitMQ\Message\MessageInterface;
use Girni\LaravelRabbitMQ\Consumer\ConsumerInterface;

class PingJobConsumer implements ConsumerInterface
{
    public function __construct(MessageInterface $message)
    {
        parent::__construct($message);
    }

    public function name(): string
    {
        return 'telegram:ping';
    }

}