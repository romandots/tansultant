<?php

namespace App\Components\Person\Exceptions;

use App\Components\Loader;
use App\Components\Person\Formatter;
use App\Exceptions\AlreadyExistsException;
use App\Models\Person;

class PersonAlreadyRegistered extends AlreadyExistsException
{
    public function __construct(Person $existingRecord)
    {
        $formattedRecord = Loader::people()->format($existingRecord, Formatter::class);
        parent::__construct($formattedRecord, 'person_with_such_bio_already_exists');
    }
}