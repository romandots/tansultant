<?php

namespace App\Common;

use App\Common\Contracts\PaginatedInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseComponentFacade extends BaseFacade
{
    protected BaseComponentService $service;

    public function __construct(string $serviceClass)
    {
        $this->service = \app($serviceClass);
    }

    public function getService(): BaseComponentService
    {
        return $this->service;
    }

    public function getRepository(): BaseComponentRepository
    {
        return $this->getService()->getRepository();
    }

    /**
     * Search by query
     * Method only return index of elements with
     * $valueField as keys and $labelField as values
     *
     * @param string|null $query
     * @param string|\Closure $labelField
     * @param string|\Closure $valueField
     * @param array $extraFields
     * @return array
     */
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

    /**
     * Method returns all records. Use with caution
     *
     * @param array $relations
     * @return Collection
     */
    public function getAll(array $relations = []): Collection
    {
        return $this->getRepository()->getAll($relations);
    }

    /**
     * Search records by defined params
     * and return paginated result
     *
     * @param PaginatedInterface $searchParams
     * @param array $relations
     * @return \Illuminate\Support\Collection
     */
    public function search(PaginatedInterface $searchParams, array $relations = []): \Illuminate\Support\Collection
    {
        return $this->getService()->search($searchParams, $relations);
    }

    /**
     * Build meta data (pagination info)
     * for defined search params
     *
     * @param PaginatedInterface $searchParams
     * @return array
     */
    public function getMeta(PaginatedInterface $searchParams): array
    {
        return $this->getService()->getMeta($searchParams);
    }

    /**
     * Single entry point for creating new record
     * returns newly created record with relations loaded
     *
     * @param \App\Common\Contracts\DtoWithUser $dto
     * @param array $relations
     * @return Model
     * @throws \Throwable
     */
    public function create(Contracts\DtoWithUser $dto, array $relations = []): Model
    {
        return $this->getService()
            ->create($dto)
            ->load($relations);
    }

    public function find(string $id, array $relations): Model
    {
        return $this->getRepository()->find($id)->load($relations);
    }

    /**
     * Single entry point for deleting record
     *
     * @param string $id
     * @param \App\Models\User $user
     * @return void
     * @throws \Throwable
     */
    public function findAndDelete(string $id, \App\Models\User $user): void
    {
        $record = $this->getRepository()->find($id);
        $this->getService()->delete($record, $user);
    }

    /**
     * Single entry point for restoring deleted record
     *
     * @param string $id
     * @param array $relations
     * @param \App\Models\User $user
     * @return Model
     * @throws \Throwable
     */
    public function findAndRestore(string $id, array $relations, \App\Models\User $user): Model
    {
        $record = $this->getRepository()->find($id);
        $this->getService()->restore($record, $user);

        return $record->load($relations);
    }

    /**
     * Single entry point for updating record
     * returns updated record with relations loaded
     *
     * @param string $id
     * @param \App\Common\Contracts\DtoWithUser $dto
     * @param array $relations
     * @return Model
     * @throws \Throwable
     */
    public function findAndUpdate(string $id, Contracts\DtoWithUser $dto, array $relations = []): Model
    {
        $record = $this->getRepository()->find($id);
        $this->getService()->update($record, $dto);
        return $record->load($relations);
    }
}