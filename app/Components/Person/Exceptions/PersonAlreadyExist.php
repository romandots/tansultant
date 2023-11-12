<?php

namespace App\Components\Person\Exceptions;

use App\Components\Loader;
use App\Components\Person\Formatter;
use App\Exceptions\AlreadyExistsException;
use App\Models\Person;

class PersonAlreadyExist extends AlreadyExistsException
{
    protected Person $person;

    public function __construct(Person $person, string $message = 'person_already_exists')
    {
        $this->person = $person;
        $formattedRecord = Loader::people()->format($person, Formatter::class);

        parent::__construct($formattedRecord, $message);
    }

    public function getPerson(): Person
    {
        return $this->person;
    }
}