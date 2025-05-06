<?php

namespace App\Exceptions;

class SimpleValidationException extends BaseException
{
    public function __construct(public readonly string $field, public readonly string $rule)
    {
        $data = [
            $this->field => [
                ['name' => $this->rule]
            ],
        ];
        parent::__construct('validation_error', $data, 422);
    }
}