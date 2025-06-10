<?php

namespace App\Common\Traits;

use Psr\Log\LoggerInterface;

trait WithLogger
{
    protected LoggerInterface $_logger;

    protected function getLoggerPrefix(): string
    {
        return '';
    }

    protected function getLogger(): LoggerInterface
    {
        if (!isset($this->_logger)) {
            $this->_logger = \app('log');
        }

        return $this->_logger;
    }

    protected function debug(string $message, array $context = []): void
    {
        $prefix = $this->getLoggerPrefix();
        $message = $prefix ? $prefix . ': ' . $message : $message;
        try {
            $this->getLogger()->debug($message, $context);
        } catch(\Throwable) {}
    }

    protected function error(string $message, array|\Throwable $context = []): void
    {
        if (is_object($context)) {
            $context = [
                'exception_message' => $context->getMessage(),
                'exception_trace' => $context->getTraceAsString(),
            ];
        }

        $prefix = $this->getLoggerPrefix();
        $message = $prefix ? $prefix . ': ' . $message : $message;
        $this->getLogger()->error($message, $context);
    }
}