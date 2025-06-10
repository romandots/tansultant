<?php

namespace App\Exceptions;

class UserAssistanceRequiredException extends BaseException
{
    public function __construct(array $options, string $message = 'user_assistance_required')
    {
        parent::__construct($message, $options, 409);
    }
}