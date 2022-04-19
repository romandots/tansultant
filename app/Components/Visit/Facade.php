<?php

declare(strict_types=1);

namespace App\Components\Visit;

use App\Common\BaseFacade;
use Illuminate\Support\Collection;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Visit> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Visit> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\Contracts\PaginatedInterface $searchParams)
 * @method \App\Models\Visit create(Dto $dto, array $relations = [])
 * @method \App\Models\Visit find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\Visit findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Visit findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function visitsArePaid(Collection $visits): bool
    {
        //
    }
}