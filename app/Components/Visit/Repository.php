<?php

declare(strict_types=1);

namespace App\Components\Visit;

use App\Models\Visit;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Visit make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Visit> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Visit find(string $id)
 * @method Visit findTrashed(string $id)
 * @method Visit create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Visit $record)
 * @method void restore(Visit $record)
 * @method void forceDelete(Visit $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Visit::class,
            ['name']
        );
    }

    /**
     * @param Visit $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->student_id = $dto->student_id;
        $record->manager_id = $dto->getUser()->id;
        $record->event_type = $dto->event_type;
        $record->event_id = $dto->event_id;
        $record->payment_type = $dto->payment_type;
        $record->payment_id = $dto->payment_id;
    }
}