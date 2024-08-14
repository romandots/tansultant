<?php

namespace App\Jobs\Telegram;

use Girni\LaravelRabbitMQ\Message\MessageInterface;
use Girni\LaravelRabbitMQ\Producer\AbstractProducer;

class PingJobProducer extends AbstractProducer
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