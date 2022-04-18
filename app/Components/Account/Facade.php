<?php

declare(strict_types=1);

namespace App\Components\Account;

use App\Common\BaseFacade;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Account> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Account> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\Contracts\PaginatedInterface $searchParams)
 * @method \App\Models\Account create(Dto $dto, array $relations = [])
 * @method \App\Models\Account find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\Account findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Account findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }
}