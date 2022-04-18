<?php

declare(strict_types=1);

namespace App\Components\Instructor;

use App\Common\BaseService;
use App\Components\Customer\Dto;
use App\Models\Customer;
use App\Models\Instructor;
use App\Models\Person;

/**
 * @method Repository getRepository()
 */
class Service extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            Instructor::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * @param Person $person
     * @return Instructor
     */
    public function createFromPerson(Person $person): Instructor
    {
        $dto = new Dto();
        $dto->name = $dto->name ?? \trans('person.instructor_name', $person->compactName());
        $dto->person_id = $person->id;

        return $this->getRepository()->create($dto);
    }
}