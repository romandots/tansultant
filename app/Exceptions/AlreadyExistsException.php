<?php

namespace App\Exceptions;

class AlreadyExistsException extends BaseException
{
    public function __construct(array $record, string $message = 'already_exists')
    {
        parent::__construct($message, $record, 409);
    }
}