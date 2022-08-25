<?php

namespace App\Exceptions;

class AlreadyExistsException extends BaseException
{
    public function __construct(array $data, string $message = 'already_exists')
    {
        parent::__construct($message, $data, 409);
    }
}