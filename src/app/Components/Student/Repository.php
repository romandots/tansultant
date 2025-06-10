<?php

declare(strict_types=1);

namespace App\Components\Student;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Student make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Student> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Student find(string $id)
 * @method Student findTrashed(string $id)
 * @method Student create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Student $record)
 * @method void restore(Student $record)
 * @method void forceDelete(Student $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Student::class,
            ['name']
        );
    }

    /**
     * @param Student $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->status = $dto->status;
        $record->card_number = $dto->card_number;
        $record->person_id = $dto->person_id;
        $record->customer_id = $dto->customer_id;
    }

    public function updateLastSeenTimestamp(string $id, ?Carbon $date = null): void
    {
        $this->getQuery()
            ->where('id', $id)
            ->update(['seen_at' => $date]);
    }
}