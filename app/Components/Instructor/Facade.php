<?php

declare(strict_types=1);

namespace App\Components\Instructor;

use App\Common\BaseComponentFacade;
use App\Models\Instructor;
use App\Models\Person;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Instructor> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Instructor> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\Contracts\PaginatedInterface $searchParams)
 * @method \App\Models\Instructor create(Dto $dto, array $relations = [])
 * @method \App\Models\Instructor find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\Instructor findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Instructor findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function createFromPerson(Person $person): Instructor
    {
        return $this->getService()->createFromPerson($person);
    }
}