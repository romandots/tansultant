<?php

namespace App\Components\Person\Exceptions;

use App\Components\Loader;
use App\Components\Person\Formatter;
use App\Exceptions\AlreadyExistsException;
use App\Models\Person;

class PhoneAlreadyRegistered extends AlreadyExistsException
{
    public function __construct(Person $existingOptions)
    {
        $formattedRecord = Loader::people()->format($existingOptions, Formatter::class);
        parent::__construct($formattedRecord, 'person_with_such_phone_already_exists');
    }
}