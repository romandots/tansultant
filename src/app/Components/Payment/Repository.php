<?php

declare(strict_types=1);

namespace App\Components\Payment;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Payment make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Payment> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Payment find(string $id)
 * @method Payment findTrashed(string $id)
 * @method Payment create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Payment $record)
 * @method void restore(Payment $record)
 * @method void forceDelete(Payment $record)
 * @mixin \App\Common\BaseComponentRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Payment::class,
            ['name']
        );
    }

    /**
     * @param Payment $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->amount = $dto->amount;
        $record->credit_id = $dto->credit_id;
        $record->bonus_id = $dto->bonus_id;
    }
}