<?php

declare(strict_types=1);

namespace App\Components\Student;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Models\Person;
use App\Models\Student;
use App\Models\User;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Student> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Student> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Student create(Dto $dto, array $relations = [])
 * @method \App\Models\Student find(ShowDto|string $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Student findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Student findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    /**
     * @param Dto $dto
     * @param Person $person
     * @return Student
     */
    public function createFromPerson(Dto $dto, Person $person): Student
    {
        return $this->getService()->createFromPerson($dto, $person);
    }

    public function activatePotentialStudent(Student $student, User $user): void
    {
        $this->getService()->activatePotentialStudent($student, $user);
    }

    public function updateLastSeen(Student $student): void
    {
        $this->getService()->updateLastSeen($student);
    }
}