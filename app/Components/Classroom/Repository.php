<?php

declare(strict_types=1);

namespace App\Components\Classroom;

use App\Common\DTO\SearchFilterDto;
use App\Http\Requests\ManagerApi\DTO\SearchClassroomsFilterDto;
use App\Models\Classroom;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Classroom make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Classroom> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Classroom find(string $id)
 * @method Classroom findTrashed(string $id)
 * @method Classroom create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Classroom $record)
 * @method void restore(Classroom $record)
 * @method void forceDelete(Classroom $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Classroom::class,
            ['name']
        );
    }

    public function getFilterQuery(
        SearchFilterDto $filter,
        array $relations = [],
        array $countRelations = []
    ): \Illuminate\Database\Eloquent\Builder {
        $query = parent::getFilterQuery($filter, $relations, $countRelations);

        assert($filter instanceof SearchClassroomsFilterDto);

        if ($filter->branch_id) {
            $query->where('branch_id', $filter->branch_id);
        }

        return $query
            ->orderBy('number', 'asc');
    }


    /**
     * @param Classroom $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->branch_id = $dto->branch_id;
        $record->color = $dto->color;
        $record->capacity = $dto->capacity;
        $record->number = $dto->number;
    }

    /**
     * @param string $branchId
     * @return \Illuminate\Database\Eloquent\Collection<Classroom>
     */
    public function getByBranchId(string $branchId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getQuery()
            ->whereNull('deleted_at')
            ->where('branch_id', $branchId)
            ->with('branch')
            ->get();
    }

    public function getBranchIdByClassroomId(string $classroomId): string
    {
        return (string)$this
            ->getQuery()
            ->where('id', $classroomId)
            ->value('branch_id');
    }
}