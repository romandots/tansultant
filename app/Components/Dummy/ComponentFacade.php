<?php

declare(strict_types=1);

namespace App\Components\Dummy;

use App\Common\BaseComponentFacade;

/**
 * @method ComponentService getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Dummy> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Dummy> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Dummy create(Dto $dto, array $relations = [])
 * @method \App\Models\Dummy find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\Dummy findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Dummy findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(ComponentService::class);
    }
}