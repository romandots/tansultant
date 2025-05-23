<?php

namespace App\Services\Import\Traits;

use App\Components\Loader;
use App\Components\Person\Dto as PersonDto;
use App\Components\Person\Exceptions\PersonAlreadyExist;
use App\Services\Import\ImportContext;

trait PersonTrait
{

    protected function getPerson(PersonDto $personDto, ImportContext $ctx): \App\Models\Person
    {
        try {
            $person = Loader::people()->create($personDto);
            $ctx->manager->increaseCounter('person');
            $ctx->debug("Создали профиль {$person->last_name} {$person->first_name} → #{$person->id}");
        } catch (PersonAlreadyExist $alreadyExist) {
            $person = $alreadyExist->getPerson();
        }
        return $person;
    }
}