<?php

declare(strict_types=1);

namespace App\Components\Student;

use App\Models\Enum\StudentStatus;
use App\Models\Person;
use App\Models\Student;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Student::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * @param Dto $dto
     * @param Person $person
     * @return Student
     */
    public function createFromPerson(Dto $dto, Person $person): Student
    {
        $dto->name = $dto->name ?? \trans('person.student_name', $person->compactName());
        $dto->person_id = $person->id;
        $dto->status = StudentStatus::POTENTIAL;

        return $this->getRepository()->create($dto);
    }
}