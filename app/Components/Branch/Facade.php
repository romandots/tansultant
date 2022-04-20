<?php

declare(strict_types=1);

namespace App\Components\Branch;

use App\Common\BaseComponentFacade;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Branch> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Branch> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\Contracts\PaginatedInterface $searchParams)
 * @method \App\Models\Branch create(Dto $dto, array $relations = [])
 * @method \App\Models\Branch find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\Branch findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Branch findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function getNextNumberValue(): int
    {
        return $this->getRepository()->getNextNumberValue();
    }
}