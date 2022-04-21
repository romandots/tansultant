<?php

declare(strict_types=1);

namespace App\Components\Student;

use App\Common\Contracts;
use App\Components\Loader;
use App\Models\Enum\StudentStatus;
use App\Models\Person;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

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
     * @return Model
     * @throws \Throwable
     */
    public function create(Contracts\DtoWithUser $dto): Model
    {
        $person = Loader::people()->find($dto->person_id);
        return $this->createFromPerson($dto, $person);
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