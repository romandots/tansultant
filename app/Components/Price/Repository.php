<?php

declare(strict_types=1);

namespace App\Components\Price;

use App\Models\Price;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Price make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Price> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Price find(string $id)
 * @method Price findTrashed(string $id)
 * @method Price create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Price $record)
 * @method void restore(Price $record)
 * @method void forceDelete(Price $record)
 * @mixin \App\Common\BaseComponentRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Price::class,
            ['name']
        );
    }

    /**
     * @param Price $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->price = $dto->price;
    }
}