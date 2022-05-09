<?php

declare(strict_types=1);

namespace App\Components\Bonus;

use App\Models\Bonus;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Bonus make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Bonus> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Bonus find(string $id)
 * @method Bonus findTrashed(string $id)
 * @method Bonus create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Bonus $record)
 * @method void restore(Bonus $record)
 * @method void forceDelete(Bonus $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Bonus::class,
            ['name']
        );
    }

    /**
     * @param Bonus $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
    }
}