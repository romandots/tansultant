<?php

declare(strict_types=1);

namespace App\Components\Formula;

use App\Models\Formula;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Formula make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Formula> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Formula find(string $id)
 * @method Formula findTrashed(string $id)
 * @method Formula create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Formula $record)
 * @method void restore(Formula $record)
 * @method void forceDelete(Formula $record)
 * @mixin \App\Common\BaseComponentRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Formula::class,
            ['name']
        );
    }

    /**
     * @param Formula $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->equation = $dto->equation;
    }
}