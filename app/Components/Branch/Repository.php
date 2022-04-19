<?php

declare(strict_types=1);

namespace App\Components\Branch;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Branch make()
 * @method int countFiltered(\App\Common\Contracts\FilteredInterface $search)
 * @method \Illuminate\Database\Eloquent\Collection<Branch> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Branch find(string $id)
 * @method Branch findTrashed(string $id)
 * @method Branch create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Branch $record)
 * @method void restore(Branch $record)
 * @method void forceDelete(Branch $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Branch::class,
            ['name']
        );
    }

    /**
     * @param Branch $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->summary = $dto->summary;
        $record->description = $dto->description;
        $record->phone = $dto->phone;
        $record->email = $dto->email;
        $record->url = $dto->url;
        $record->vk_url = $dto->vk_url;
        $record->facebook_url = $dto->facebook_url;
        $record->telegram_username = $dto->telegram_username;
        $record->instagram_username = $dto->instagram_username;
        $record->address = [
            'country' => $dto->address['country'] ?? null,
            'city' => $dto->address['city'] ?? null,
            'street' => $dto->address['street'] ?? null,
            'building' => $dto->address['building'] ?? null,
            'coordinates' => isset($dto->address['coordinates'][0], $dto->address['coordinates'][1])
                ? [$dto->address['coordinates'][0], $dto->address['coordinates'][1]] : null,
        ];
        $record->number = $dto->number;
    }

    /**
     * @return int
     */
    public function getNextNumberValue(): int
    {
        return (int)($this->getQuery()->max('number')) + 1;
    }
}