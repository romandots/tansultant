<?php

namespace App\Services;

use App\Http\Requests\DTO\Contracts\PaginatedInterface;
use App\Repository\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseFacade
{
    abstract public function getService(): BaseService;

    public function getRepository(): BaseRepository
    {
        return $this->getService()->getRepository();
    }

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

    public function search(PaginatedInterface $searchParams, array $relations = []): \Illuminate\Support\Collection
    {
        return $this->getService()->search($searchParams, $relations);
    }

    public function getMeta(PaginatedInterface $searchParams): array
    {
        return $this->getService()->getMeta($searchParams);
    }

    public function find(string $id, array $relations = []): Model
    {
        return $this->getRepository()->find($id);
    }

    public function findAndDelete(string $id): void
    {
        $record = $this->getRepository()->find($id);
        $this->service->delete($record);
    }

    public function restore(string $id): void
    {
        $record = $this->getRepository()->findTrashed($id);
        $this->getRepository()->restore($record);
    }
}