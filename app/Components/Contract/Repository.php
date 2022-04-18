<?php

declare(strict_types=1);

namespace App\Components\Contract;

use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Contract make()
 * @method int countFiltered(\App\Common\Contracts\FilteredInterface $search)
 * @method \Illuminate\Database\Eloquent\Collection<Contract> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Contract find(string $id)
 * @method Contract findTrashed(string $id)
 * @method Contract create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Contract $record)
 * @method void restore(Contract $record)
 * @method void forceDelete(Contract $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseRepository
{
    public function __construct() {
        parent::__construct(
            Contract::class,
            ['name']
        );
    }

    /**
     * @param Contract $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\Dto $dto): void
    {
        $record->serial = $dto->serial ?: $this->getCurrentSerial();
        $record->number = $this->getNextValue($record->serial);
        $record->branch_id = $dto->branch_id;
        $record->customer_id = $dto->customer_id;
        $record->status = Contract::STATUS_PENDING;
    }

    /**
     * @param string $customerId
     * @return Contract|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByCustomerId(string $customerId): ?Contract
    {
        return $this->getQuery()->where('customer_id', $customerId)->firstOrFail();
    }

    public function sign(Contract $contract): void
    {
        $contract->status = Contract::STATUS_SIGNED;
        $contract->signed_at = Carbon::now();
        $contract->updated_at = Carbon::now();
        $contract->save();
    }

    public function terminate(Contract $contract): void
    {
        $contract->status = Contract::STATUS_TERMINATED;
        $contract->terminated_at = Carbon::now();
        $contract->updated_at = Carbon::now();
        $contract->save();
    }

    private function getCurrentSerial(): string
    {
        $lastRecord = \DB::table(Contract::TABLE)
            ->select('serial')
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->first();

        return $lastRecord ? $lastRecord->serial : '';
    }

    private function getNextValue(?string $serial = null): int
    {
        /** @var Builder $query */
        $query = \DB::table(Contract::TABLE)
            ->select('number');

        if ($serial) {
            $query = $query->where('serial', $serial);
        }

        $lastRecord = $query
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->first();

        return 1 + (int)($lastRecord->number ?? 0);
    }
}