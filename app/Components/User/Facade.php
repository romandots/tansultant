<?php

declare(strict_types=1);

namespace App\Components\User;

use App\Common\BaseComponentFacade;
use App\Models\Person;
use App\Models\User;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\User> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\User> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\Contracts\PaginatedInterface $searchParams)
 * @method \App\Models\User create(Dto $dto, array $relations = [])
 * @method \App\Models\User find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\User findAndRestore(string $id, array $relations = [])
 * @method \App\Models\User findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function createFromPerson(Dto $dto, Person $person): User
    {
        return $this->getService()->createFromPerson($dto, $person);
    }
}