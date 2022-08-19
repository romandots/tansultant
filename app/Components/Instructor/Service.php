<?php

declare(strict_types=1);

namespace App\Components\Instructor;

use App\Common\Contracts;
use App\Components\Loader;
use App\Models\Instructor;
use App\Models\Person;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
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

    public function create(Contracts\DtoWithUser $dto): Model
    {
        assert($dto instanceof Dto);
        $person = Loader::people()->find($dto->person_id);
        return $this->createFromPerson($dto, $person);
    }

    public function update(Model $record, Contracts\DtoWithUser $dto): void
    {
        assert($dto instanceof Dto);
        $person = Loader::people()->find($dto->person_id);
        $this->updateFromPerson($record, $dto, $person);
    }

    /**
     * @param Dto $dto
     * @param Person $person
     * @return Instructor
     */
    public function createFromPerson(Dto $dto, Person $person): Instructor
    {
        $this->validatePerson($person, null);

        $dto->name = $dto->name ?? \trans('person.instructor_name', $person->compactName());
        $dto->person_id = $person->id;

        return $this->getRepository()->create($dto);
    }

    /**
     * @param Instructor $record
     * @param Dto $dto
     * @param Person $person
     * @return void
     */
    public function updateFromPerson(Instructor $record, Dto $dto, Person $person): void
    {
        $this->validatePerson($person, $record);

        $dto->name = $dto->name ?? \trans('person.instructor_name', $person->compactName());
        $dto->person_id = $person->id;

        $this->getRepository()->update($record, $dto);
    }

    private function validatePerson(Person $person, ?Instructor $instructor): void
    {
        $person->load('instructors');
        if ($person->instructor && $person->instructor->id !== $instructor?->id) {
            throw new Exceptions\InstructorAlreadyExists($person->instructor);
        }
    }
}