<?php

namespace App\Services\Notification\Providers;

abstract class Provider
{
    public array $recipients = [];
    public array $options = [];

    public function to(string|array $recipients): self
    {
        $this->recipients = is_array($recipients) ? $recipients : [$recipients];
        return $this;
    }

    public function withOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    abstract public function send(string $message): void;
}