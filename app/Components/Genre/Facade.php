<?php

declare(strict_types=1);

namespace App\Components\Genre;

use App\Common\BaseComponentFacade;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Genre> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Genre> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Genre create(Dto $dto, array $relations = [])
 * @method \App\Models\Genre find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\Genre findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Genre findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }
}