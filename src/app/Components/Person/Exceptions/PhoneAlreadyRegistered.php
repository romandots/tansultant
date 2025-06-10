<?php

namespace App\Components\Person\Exceptions;

use App\Models\Person;

class PhoneAlreadyRegistered extends PersonAlreadyExist
{
    public function __construct(Person $person)
    {
        parent::__construct($person, 'person_with_such_phone_already_exists');
    }
}