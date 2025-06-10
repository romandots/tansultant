<?php

namespace App\Components\Person\Exceptions;

use App\Models\Person;

class PersonAlreadyRegistered extends PersonAlreadyExist
{
    public function __construct(Person $person)
    {
        parent::__construct($person, 'person_with_such_bio_already_exists');
    }
}