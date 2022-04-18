<?php

namespace App\Common;

use App\Common\Contracts\FilteredInterface;
use App\Common\Contracts\PaginatedInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseRepository
{
    public function __construct(
        protected string $modelClass,
        protected array $searchableAttributes = [],
    ) {
    }

    abstract public function fill(Model $record, Contracts\DtoWithUser $dto): void;

    public function getSearchableAttributes(): array
    {
        return $this->searchableAttributes;
    }

    public function withSoftDeletes(): bool
    {
        return isset(class_uses($this->modelClass)[SoftDeletes::class]);
    }

    public function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->modelClass::query();
    }

    public function make(): Model
    {
        return new $this->modelClass();
    }

    protected function getFilterQuery(FilteredInterface $filter): \Illuminate\Database\Eloquent\Builder
    {
        $query = $this->getQuery();

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

    public function getSuggestQuery(FilteredInterface $filter): \Illuminate\Database\Eloquent\Builder
    {
        return $this->getFilterQuery($filter);
    }

    public function countFiltered(FilteredInterface $search): int
    {
        return $this->getFilterQuery($search)->count();
    }

    protected function getFilteredQuery(FilteredInterface $filter, array $withRelations = []): \Illuminate\Database\Eloquent\Builder
    {
        return $this->getFilterQuery($filter)
            ->with($withRelations);
    }

    public function findFilteredPaginated(PaginatedInterface $search, array $withRelations = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getFilteredQuery($search->filter, $withRelations)
            ->orderBy($search->sort, $search->order)
            ->offset($search->offset)
            ->limit($search->limit)
            ->get();
    }

    public function find(string $id): Model
    {
        $query = $this->getQuery();
        if ($this->withSoftDeletes()) {
            $query->whereNull('deleted_at');
        }
        return $query
            ->where('id', $id)
            ->firstOrFail();
    }

    public function findTrashed(string $id): Model
    {
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
        $record->save();
        return $record;
    }

    public function update($record, Contracts\DtoWithUser $dto): void
    {
        $this->fill($record, $dto);
        $this->fillDate($record, 'updated_at');
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