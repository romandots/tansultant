<?php

namespace App\Common;

use App\Common\DTO\SearchDto;
use App\Common\DTO\SearchFilterDto;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Ramsey\Uuid\Uuid;
use Spatie\MediaLibrary\HasMedia;

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

    public function getQuery(
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

    public function getFilterQuery(
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

    public function countFiltered(SearchFilterDto $search): int
    {
        return $this->getFilterQuery($search)->count();
    }

    public function findFilteredPaginated(
        SearchDto $search,
        array $withRelations = [],
        array $withCountRelations = []
    ): \Illuminate\Database\Eloquent\Collection {
        return $this->getFilterQuery($search->getFilter(), $withRelations, $withCountRelations)
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
     * @throws \BadMethodCallException
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

    public function save(Model $record, array $dates = []): void
    {
        if ($record::UPDATED_AT) {
            if (Arr::isAssoc($dates)) {
                $dates[$record::UPDATED_AT] = Carbon::now();
            } else {
                $dates[] = $record::UPDATED_AT;
            }
        }

        $this->fillDates($record, $dates);
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

    public function getMany(iterable $ids, array $relations = [], string $key = 'id'): Collection
    {
        return $this->getQuery()
            ->whereIn($key, $ids)
            ->with($relations)
            ->get();
    }

    protected function fillDates(Model $record, array $datesToUpdate): void
    {
        $isAssoc = Arr::isAssoc($datesToUpdate);
        foreach ($datesToUpdate as $key => $value) {
            $dateField = $isAssoc ? $key : $value;
            $date = $isAssoc ? $value : \Carbon\Carbon::now();
            if (!$dateField) {
                continue;
            }
            $this->fillDate($record, $dateField, $date);
        }
    }

    protected function fillDate(Model $record, string $attribute, ?\Carbon\Carbon $date = null): void
    {
        $record->{$attribute} = $date ?? \Carbon\Carbon::now();
    }

    protected function attachRelations(Model $model, string $relation, iterable $relatedObjects, array $additional = []): void
    {
        $model->load($relation);
        foreach ($relatedObjects as $relationObject) {
            if ($model->{$relation}->where('id', $relationObject->id)->count()) {
                continue;
            }

            $model->{$relation}()->attach($relationObject->id, $additional);
        }
    }

    protected function detachRelations(Model $model, string $relation, iterable $relationObjects): void
    {
        $model->load($relation);
        foreach ($relationObjects as $relationObject) {
            if ($relationObject->{$relation}?->where('id', $relationObject->id)->count() === 0) {
                continue;
            }

            $model->{$relation}()->detach($relationObject->id);
        }
    }

    public function setStatus(Model $record, object $status, array $datesToUpdate = []): void
    {
        $record->status = $status;
        if ($record::UPDATED_AT) {
            if (Arr::isAssoc($datesToUpdate)) {
                $datesToUpdate[$record::UPDATED_AT] = Carbon::now();
            } else {
                $datesToUpdate[] = $record::UPDATED_AT;
            }
        }

        $this->fillDates($record, $datesToUpdate);
    }

    public function updateStatus(Model $record, object $status, array $datesToUpdate = []): void
    {
        $this->setStatus($record, $status, $datesToUpdate);
        $this->save($record);
    }

    public function addDocument(HasMedia $record, string $stream, string $collection = 'default'): void
    {
        $record
            ->clearMediaCollection($collection)
            ->addMediaFromStream($stream)
            ->toMediaCollection($collection);
    }
}