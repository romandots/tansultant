<?php

declare(strict_types=1);

namespace App\Components\Hold;

use App\Models\Hold;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Hold make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Hold> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Hold find(string $id)
 * @method Hold findTrashed(string $id)
 * @method Hold create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Hold $record)
 * @method void restore(Hold $record)
 * @method void forceDelete(Hold $record)
 * @mixin \App\Common\BaseComponentRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Hold::class,
            ['name']
        );
    }

    /**
     * @param Hold $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->subscription_id = $dto->subscription_id;
    }

    public function endHold(Hold $hold): void
    {
        $this->save($hold, ['ends_at']);
    }
}