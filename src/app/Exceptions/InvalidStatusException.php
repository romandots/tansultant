<?php

namespace App\Exceptions;

class InvalidStatusException extends BaseException
{
    public function __construct(string $currentStatus, array $allowedStatuses = [], string $message = 'invalid_status')
    {
        parent::__construct($message, [
            'status' => $currentStatus,
            'allowed' => array_map(static fn(object $status) => $status->value, $allowedStatuses),
        ], 409);
    }
}