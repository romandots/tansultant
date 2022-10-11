<?php

declare(strict_types=1);

namespace App\Components\Credit;

use App\Models\Credit;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Credit make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Credit> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Credit find(string $id)
 * @method Credit findTrashed(string $id)
 * @method Credit create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Credit $record)
 * @method void restore(Credit $record)
 * @method void forceDelete(Credit $record)
 * @mixin \App\Common\BaseComponentRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Credit::class,
            ['name']
        );
    }

    /**
     * @param Credit $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->amount = $dto->amount;
        $record->customer_id = $dto->customer_id;
        $record->transaction_id = $dto->transaction_id;
    }

    public function getCreditsSumByCustomerId(string $customerId): int
    {
        return (int)$this->getQuery()->where('customer_id', $customerId)->sum('amount');
    }
}