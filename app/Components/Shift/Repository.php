<?php

declare(strict_types=1);

namespace App\Components\Shift;

use App\Models\Enum\ShiftStatus;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Shift make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Shift> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Shift find(string $id)
 * @method Shift findTrashed(string $id)
 * @method Shift create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Shift $record)
 * @method void restore(Shift $record)
 * @method void forceDelete(Shift $record)
 * @mixin \App\Common\BaseComponentRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Shift::class,
            ['name']
        );
    }

    /**
     * @param Shift $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->user_id = $dto->getUser()->id;
        $record->branch_id = $dto->branch_id;
        $record->status = ShiftStatus::ACTIVE;
    }

    public function close(Shift $shift, float $totalIncome): void
    {
        $shift->total_income = $totalIncome;
        $this->setStatus($shift, ShiftStatus::CLOSED, ['closed_at']);
        $this->save($shift);
    }
}