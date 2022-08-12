<?php

namespace App\Exceptions;

class SimpleValidationException extends BaseException
{
    public function __construct(string $field, string $rule)
    {
        $data = [
            $field => [
                'name' => $rule,
            ],
        ];
        parent::__construct('validation_error', $data, 422);
    }
}