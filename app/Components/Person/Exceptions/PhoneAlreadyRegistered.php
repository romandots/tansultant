<?php

namespace App\Components\Person\Exceptions;

use App\Exceptions\SimpleValidationException;
use App\Models\Person;

class PhoneAlreadyRegistered extends SimpleValidationException
{
    protected string $phone;
    protected Person $existingRecord;

    public function __construct(string $phone, Person $existingRecord)
    {
        $this->phone = $phone;
        $this->existingRecord = $existingRecord;

        parent::__construct('phone', 'unique');
    }
}