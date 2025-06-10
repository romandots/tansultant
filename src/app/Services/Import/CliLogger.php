<?php

namespace App\Services\Import;

use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;

class CliLogger implements LoggerInterface
{

    public function __construct(
        protected Command $command,
    ) {
    }

    public function emergency(\Stringable|string $message, array $context = []): void
    {
        $this->command->error($message . $this->stringifyContext($context));
    }

    public function alert(\Stringable|string $message, array $context = []): void
    {
        $this->command->alert($message . $this->stringifyContext($context));
    }

    public function critical(\Stringable|string $message, array $context = []): void
    {
        $this->command->error($message . $this->stringifyContext($context));
    }

    public function error(\Stringable|string $message, array $context = []): void
    {
        $this->command->error($message . $this->stringifyContext($context));
    }

    public function warning(\Stringable|string $message, array $context = []): void
    {
        $this->command->warn($message . $this->stringifyContext($context));
    }

    public function notice(\Stringable|string $message, array $context = []): void
    {
        $this->command->alert($message . $this->stringifyContext($context));
    }

    public function info(\Stringable|string $message, array $context = []): void
    {
        $this->command->info($message . $this->stringifyContext($context));
    }

    public function debug(\Stringable|string $message, array $context = []): void
    {
        $this->command->info($message . $this->stringifyContext($context));
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->command->line($message . $this->stringifyContext($context));
    }

    protected function stringifyContext(array $context): string|false
    {
        return $context === [] ? "" :( " " . json_encode($context));
    }
}