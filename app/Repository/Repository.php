<?php

namespace App\Repository;

use App\Http\Requests\DTO\Contracts\FilteredInterface;
use App\Http\Requests\DTO\Contracts\PaginatedInterface;
use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    public const WITH_SOFT_DELETES = false;
    public const SEARCHABLE_ATTRIBUTES = [];

    abstract protected function getQuery(): \Illuminate\Database\Eloquent\Builder;

    protected function getFilterQuery(FilteredInterface $filter): \Illuminate\Database\Eloquent\Builder
    {
        $query = $this->getQuery();

        if (self::WITH_SOFT_DELETES && !$filter->withDeleted()) {
            $query->whereNull('deleted_at');
        }

        if ($filter->query && count(self::SEARCHABLE_ATTRIBUTES) > 0) {
            $query->where(function (\Illuminate\Database\Eloquent\Builder $query) use ($filter) {
                $searchQuery = $filter->getQuery() . '%';
                $attributes = self::SEARCHABLE_ATTRIBUTES;
                $firstAttribute = array_shift($attributes);
                $query->where($firstAttribute, 'ILIKE', $searchQuery);
                foreach ($attributes as $attribute) {
                    $query->orWhere($attribute, 'ILIKE', $searchQuery);
                }
            });
        }

        return $query;
    }

    public function countFiltered(FilteredInterface $search): int
    {
        return $this->getFilterQuery($search)->count();
    }

    public function findFilteredPaginated(PaginatedInterface $search, array $withRelations = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getFilterQuery($search->filter)
            ->with($withRelations)
            ->orderBy($search->sort, $search->order)
            ->offset($search->offset)
            ->limit($search->limit)
            ->get();
    }

    public function find(string $id): Model
    {
        $query = $this->getQuery();
        if (self::WITH_SOFT_DELETES) {
            $query->whereNull('deleted_at');
        }
        return $query
            ->where('id', $id)
            ->firstOrFail();
    }

    public function findTrashed(string $id): Model
    {
        $query = $this->getQuery();
        if (self::WITH_SOFT_DELETES) {
            $query->whereNotNull('deleted_at');
        }
        return $query
            ->where('id', $id)
            ->firstOrFail();
    }

    public function delete(Model $model): void
    {
        $model->delete();
    }

    public function forceDelete(Model $model): void
    {
        if (!self::WITH_SOFT_DELETES) {
            $model->delete();
            return;
        }
        $model->forceDelete();
    }
}