<?php

namespace App\Services;

use App\Http\Requests\ManagerApi\FilteredPaginatedFormRequest;
use App\Repository\Repository;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseFacade
{
    abstract public function getRepository(): Repository;
    abstract public function getService(): BaseService;

    public function suggest(
        ?string $query,
        string|\Closure $labelField = 'name',
        string|\Closure $valueField = 'id',
        array $extraFields = []
    ): array
    {
        return $this
            ->getService()
            ->suggest($query, $labelField, $valueField, $extraFields);
    }

    public function getAll(): Collection
    {
        return $this->getRepository()->getAll();
    }
}