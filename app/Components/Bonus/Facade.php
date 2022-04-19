<?php

declare(strict_types=1);

namespace App\Components\Bonus;

use App\Common\BaseComponentFacade;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Bonus> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Bonus> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\Contracts\PaginatedInterface $searchParams)
 * @method \App\Models\Bonus create(Dto $dto, array $relations = [])
 * @method \App\Models\Bonus find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\Bonus findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Bonus findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }
}