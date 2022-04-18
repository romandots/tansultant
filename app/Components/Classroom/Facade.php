<?php

declare(strict_types=1);

namespace App\Components\Classroom;

use App\Common\BaseFacade;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Classroom> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Classroom> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\Contracts\PaginatedInterface $searchParams)
 * @method \App\Models\Classroom create(Dto $dto, array $relations = [])
 * @method \App\Models\Classroom find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\Classroom findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Classroom findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }
}