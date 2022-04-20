<?php

declare(strict_types=1);

namespace App\Components\Dummy;

use App\Models\Dummy;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Dummy make()
 * @method int countFiltered(\App\Common\Contracts\FilteredInterface $search)
 * @method \Illuminate\Database\Eloquent\Collection<Dummy> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Dummy find(string $id)
 * @method Dummy findTrashed(string $id)
 * @method Dummy create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Dummy $record)
 * @method void restore(Dummy $record)
 * @method void forceDelete(Dummy $record)
 * @mixin \App\Common\BaseComponentRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Dummy::class,
            ['name']
        );
    }

    /**
     * @param Dummy $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
    }
}