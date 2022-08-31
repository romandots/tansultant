<?php

namespace App\Common;

use App\Common\DTO\SearchDto;
use App\Common\DTO\SearchFilterDto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

abstract class BaseComponentRepository extends BaseRepository
{
    public function __construct(
        protected string $modelClass,
        protected array $searchableAttributes = [],
    ) {
    }

    abstract public function fill(Model $record, Contracts\DtoWithUser $dto): void;

    final public function getSearchableAttributes(): array
    {
        return $this->searchableAttributes;
    }

    final public function withSoftDeletes(): bool
    {
        return isset(class_uses($this->modelClass)[SoftDeletes::class]);
    }

    final public function getQuery(
        array $relations = [],
        array $countRelations = []
    ): \Illuminate\Database\Eloquent\Builder {
        $query = $this->modelClass::query()
            ->with($relations)
            ->withCount($countRelations);

        if ($this->withSoftDeletes()) {
            $query->withTrashed();
        }

        return $query;
    }

    final public function make(): Model
    {
        return new $this->modelClass();
    }

    public function getSuggestQuery(
        SearchFilterDto $filter,
        array $relations = [],
        array $countRelations = []
    ): \Illuminate\Database\Eloquent\Builder {
        $query = $this->getQuery($relations, $countRelations);

        if ($this->withSoftDeletes() && !$filter->withDeleted()) {
            $query->whereNull('deleted_at');
        }

        if ($filter->query && count($this->getSearchableAttributes()) > 0) {
            $query->where(function (\Illuminate\Database\Eloquent\Builder $query) use ($filter) {
                $searchQuery = '%' . $filter->getQuery() . '%';
                $attributes = $this->getSearchableAttributes();
                $firstAttribute = array_shift($attributes);
                $query->where($firstAttribute, 'ILIKE', $searchQuery);
                foreach ($attributes as $attribute) {
                    $query->orWhere($attribute, 'ILIKE', $searchQuery);
                }
            });
        }

        return $query;
    }

    protected function getFilterQuery(
        SearchFilterDto $filter,
        array $relations = [],
        array $countRelations = []
    ): \Illuminate\Database\Eloquent\Builder {
        return $this->getSuggestQuery($filter, $relations, $countRelations);
    }

    public function countFiltered(SearchFilterDto $search): int
    {
        return $this->getFilterQuery($search)->count();
    }

    protected function getFilteredQuery(
        SearchFilterDto $filter,
        array $withRelations = [],
        array $withCountRelations = []
    ): \Illuminate\Database\Eloquent\Builder {
        return $this->getFilterQuery($filter, $withRelations, $withCountRelations);
    }

    public function findFilteredPaginated(
        SearchDto $search,
        array $withRelations = [],
        array $withCountRelations = []
    ): \Illuminate\Database\Eloquent\Collection {
        return $this->getFilteredQuery($search->getFilter(), $withRelations, $withCountRelations)
            ->orderBy($search->getSort(), $search->getOrder())
            ->offset($search->getOffset())
            ->limit($search->getLimit())
            ->get();
    }

    protected function validateUuid($id): void
    {
        if (!Uuid::isValid($id)) {
            throw new ModelNotFoundException();
        }
    }

    /**
     * @param string $id
     * @param array $relations
     * @param array $countRelations
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<\Illuminate\Database\Eloquent\Model>
     */
    final public function find(string $id, array $relations = [], array $countRelations = []): Model
    {
        $this->validateUuid($id);
        $query = $this->getQuery();
        if ($this->withSoftDeletes()) {
            $query->whereNull('deleted_at');
        }
        return $query
            ->where('id', $id)
            ->with($relations)
            ->withCount($countRelations)
            ->firstOrFail();
    }

    final public function findTrashed(string $id): Model
    {
        $this->validateUuid($id);
        $query = $this->getQuery();
        if ($this->withSoftDeletes()) {
            $query->whereNotNull('deleted_at');
        }
        return $query
            ->where('id', $id)
            ->firstOrFail();
    }

    public function create(Contracts\DtoWithUser $dto): Model
    {
        $record = $this->make();
        $record->id = \uuid();
        $this->fill($record, $dto);
        $this->fillDate($record, 'created_at');
        $this->save($record);
        return $record;
    }

    public function update(Model $record, Contracts\DtoWithUser $dto): void
    {
        $this->fill($record, $dto);
        $this->save($record);
    }

    public function save(Model $record): void
    {
        if ($record::UPDATED_AT !== null) {
            $this->fillDate($record, 'updated_at');
        }
        $record->save();
    }

    public function delete(Model $record): void
    {
        if ($this->withSoftDeletes()) {
            $this->fillDate($record, 'updated_at');
            $this->fillDate($record, 'deleted_at');
            $record->save();
            return;
        }

        $record->delete();
    }

    public function restore(Model $record): void
    {
        if (!$this->withSoftDeletes()) {
            return;
        }
        $this->fillDate($record, 'updated_at');
        $record->deleted_at = null;
        $record->save();
    }

    public function forceDelete(Model $record): void
    {
        if (!$this->withSoftDeletes()) {
            $record->delete();
            return;
        }
        $record->forceDelete();
    }

    public function getAll(array $relations = []): Collection
    {
        return $this->getQuery()->with($relations)->get();
    }

    protected function fillDate(Model $record, string $attribute): void
    {
        $record->{$attribute} = \Carbon\Carbon::now();
    }
}