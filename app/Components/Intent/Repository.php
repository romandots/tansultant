<?php

declare(strict_types=1);

namespace App\Components\Intent;

use App\Models\Enum\IntentStatus;
use App\Models\Intent;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Intent make()
 * @method int countFiltered(\App\Common\Contracts\FilteredInterface $search)
 * @method \Illuminate\Database\Eloquent\Collection<Intent> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Intent find(string $id)
 * @method Intent findTrashed(string $id)
 * @method Intent create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Intent $record)
 * @method void restore(Intent $record)
 * @method void forceDelete(Intent $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Intent::class,
            ['name']
        );
    }

    /**
     * @param Intent $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->manager_id = $dto->getUser()->id;
        $record->status = $dto->status;
        $record->student_id = $dto->student_id;
        $record->event_id = $dto->event_id;
        $record->event_type = $dto->event_type;
    }

    public function setVisited(Intent $intent): void
    {
        $intent->status = IntentStatus::VISITED;
        $intent->updated_at = \Carbon\Carbon::now();
        $intent->save();
    }

    public function setNoShow(Intent $intent): void
    {
        $intent->status = IntentStatus::NOSHOW;
        $intent->updated_at = \Carbon\Carbon::now();
        $intent->save();
    }
}