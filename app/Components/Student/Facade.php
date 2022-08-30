<?php

declare(strict_types=1);

namespace App\Components\Student;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Models\Person;
use App\Models\Student;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Student> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Student> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Student create(Dto $dto, array $relations = [])
 * @method \App\Models\Student find(ShowDto $showDto)
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
}