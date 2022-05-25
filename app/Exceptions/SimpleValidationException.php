<?php

namespace App\Exceptions;

class SimpleValidationException extends BaseException
{
    public function __construct(?string $message = null, array $data = [])
    {
        parent::__construct($message ?? 'validation_error', $data, 422);
    }
}