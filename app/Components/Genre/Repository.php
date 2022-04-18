<?php

declare(strict_types=1);

namespace App\Components\Genre;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Genre make()
 * @method int countFiltered(\App\Common\Contracts\FilteredInterface $search)
 * @method \Illuminate\Database\Eloquent\Collection<Genre> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Genre find(string $id)
 * @method Genre findTrashed(string $id)
 * @method Genre create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Genre $record)
 * @method void restore(Genre $record)
 * @method void forceDelete(Genre $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseRepository
{
    public function __construct() {
        parent::__construct(
            Genre::class,
            ['name']
        );
    }

    /**
     * @param Genre $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
    }
}