<?php

declare(strict_types=1);

namespace App\Components\Instructor;

use App\Models\Instructor;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Instructor make()
 * @method int countFiltered(\App\Common\Contracts\FilteredInterface $search)
 * @method \Illuminate\Database\Eloquent\Collection<Instructor> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Instructor find(string $id)
 * @method Instructor findTrashed(string $id)
 * @method Instructor create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Instructor $record)
 * @method void restore(Instructor $record)
 * @method void forceDelete(Instructor $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Instructor::class,
            ['name']
        );
    }

    /**
     * @param Instructor $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->person_id = $dto->person_id;
        $record->description = $dto->description;
        $record->display = $dto->display;
        $record->status = $dto->status ?? InstructorStatus::HIRED;
    }
}