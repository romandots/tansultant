<?php

declare(strict_types=1);

namespace App\Components\Customer;

use App\Common\BaseFacade;
use App\Models\Customer;
use App\Models\Person;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Customer> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Customer> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\Contracts\PaginatedInterface $searchParams)
 * @method \App\Models\Customer create(Dto $dto, array $relations = [])
 * @method \App\Models\Customer find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\Customer findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Customer findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function createFromPerson(Person $person): Customer
    {
        return $this->getService()->createFromPerson($person);
    }
}