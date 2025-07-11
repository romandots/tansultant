<?php

declare(strict_types=1);

namespace App\Components\LogRecord;

use App\Common\BaseComponentRepository;
use App\Models\LogRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method LogRecord make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<LogRecord> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method LogRecord find(string $id)
 * @method LogRecord findTrashed(string $id)
 * @method LogRecord create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(LogRecord $record)
 * @method void restore(LogRecord $record)
 * @method void forceDelete(LogRecord $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            LogRecord::class,
            ['name']
        );
    }

    /**
     * @param LogRecord $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        assert($dto instanceof Dto);
        $record->action = $dto->action;
        $record->message = $dto->message;
        $record->object_type = $dto->object_type;
        $record->object_id = $dto->object_id;
        $record->user_id = $dto->getUser()->id;
        $record->old_value = $dto->old_value;
        $record->new_value = $dto->new_value;
    }

    public function getAllByObjectTypeAndObjectId(\App\Models\Enum\LogRecordObjectType $objectType, string $objectId): Collection
    {
        $this->validateUuid($objectId);
        return $this
            ->getQuery()
            ->where('object_type', $objectType)
            ->where('object_id', $objectId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}