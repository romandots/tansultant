<?php

declare(strict_types=1);

namespace App\Components\Customer;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Customer make()
 * @method int countFiltered(\App\Common\Contracts\FilteredInterface $search)
 * @method \Illuminate\Database\Eloquent\Collection<Customer> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Customer find(string $id)
 * @method Customer findTrashed(string $id)
 * @method Customer create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Customer $record)
 * @method void restore(Customer $record)
 * @method void forceDelete(Customer $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Customer::class,
            ['name']
        );
    }

    /**
     * @param Customer $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->person_id = $dto->id;
    }
}