<?php

declare(strict_types=1);

namespace App\Components\Branch;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Branch> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Branch> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Branch create(Dto $dto, array $relations = [])
 * @method \App\Models\Branch find(ShowDto $showDto)
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