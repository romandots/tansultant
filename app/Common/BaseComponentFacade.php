<?php

namespace App\Common;

use App\Common\DTO\SearchDto;
use App\Common\DTO\ShowDto;
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

    public function make(array $attributes): Model
    {
        return $this->getRepository()->make($attributes);
    }

    /**
     * Search by query
     * Method only return index of elements with
     * $valueField as keys and $labelField as values
     *
     * @param null|DTO\SuggestDto $query
     * @param string|\Closure $labelField
     * @param string|\Closure $valueField
     * @param array $extraFields
     * @return array
     */
    public function suggest(
        \App\Common\DTO\SuggestDto $suggestDto,
        string|\Closure $labelField = 'name',
        string|\Closure $valueField = 'id',
        array $extraFields = []
    ): array
    {
        return $this
            ->getService()
            ->suggest($suggestDto, $labelField, $valueField, $extraFields);
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
     * Method returns many records by ids
     *
     * @param iterable $ids
     * @param array $relations
     * @return Collection
     */
    public function getMany(iterable $ids, array $relations = []): Collection
    {
        return $this->getRepository()->getMany($ids, $relations, 'id');
    }

    /**
     * Search records by defined params
     * and return paginated result
     *
     * @param SearchDto $searchParams
     * @param array $relations
     * @return \Illuminate\Support\Collection
     */
    public function search(SearchDto $searchParams, array $relations = []): \Illuminate\Support\Collection
    {
        return $this->getService()
            ->search($searchParams, $searchParams->with + $relations, $searchParams->with_count);
    }

    /**
     * Build meta data (pagination info)
     * for defined search params
     *
     * @param SearchDto $searchParams
     * @return array
     */
    public function getMeta(SearchDto $searchParams): array
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

    /**
     * @param ShowDto|string $showDto
     * @param array $relations
     * @param array $countRelations
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<\Illuminate\Database\Eloquent\Model>
     */
    public function find(ShowDto|string $showDto, array $relations = [], array $countRelations = []): Model
    {
        return is_string($showDto)
            ? $this->findById($showDto, $relations, $countRelations)
                : $this->findById($showDto->id, $showDto->with, $showDto->with_count);
    }

    /**
     * @param string $column
     * @param mixed $value
     * @param array $relations
     * @param array $countRelations
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<\Illuminate\Database\Eloquent\Model>
     */
    public function findBy(string $column, mixed $value, array $relations = [], array $countRelations = []): Model
    {
        return $this->getRepository()->findBy($column, $value, $relations, $countRelations);
    }

    /**
     * @param string $id
     * @param array $relations
     * @param array $countRelations
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<\Illuminate\Database\Eloquent\Model>
     */
    public function findById(string $id, array $relations = [], array $countRelations = []): Model
    {
        return $this->getRepository()->find($id, $relations, $countRelations);
    }

    public function getById(string $id, array $relations = [], array $countRelations = []): ?Model
    {
        try {
            return $this->findById($id, $relations, $countRelations);
        } catch (\Throwable) {
            return null;
        }
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

    public function delete(Model $record, \App\Models\User $user): void
    {
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
        $record = $this->getRepository()->findTrashed($id);
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

    public function usesSoftDeletes(): bool
    {
        return $this->getRepository()->withSoftDeletes();
    }

    public function format(Model $record, string $formatterClass): array
    {
        return (new $formatterClass($record))->toArray(\request());
    }

    public function formatCollection(iterable $collection, string $formatterClass): iterable
    {
        return ($collection instanceof \Illuminate\Support\Collection ? $collection : collect($collection))
            ->map(fn(Model $record) => $this->format($record, $formatterClass));
    }

    public function findOrSave(Model $record, array $uniqueAttributes = []): void
    {
        $this->getService()->findOrSave($record, $uniqueAttributes);
    }
}