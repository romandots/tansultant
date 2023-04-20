<?php

declare(strict_types=1);

namespace App\Components\Account;

use App\Models\{Account};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Account make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method Collection<Account> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Account find(string $id)
 * @method Account findTrashed(string $id)
 * @method Account create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Account $record)
 * @method void restore(Account $record)
 * @method void forceDelete(Account $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public const DEFAULT_ACCOUNT_TABLE = 'default_accounts';

    public function __construct() {
        parent::__construct(
            Account::class,
            ['name']
        );
    }

    /**
     * @param Account $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->branch_id = $dto->branch_id;
    }

    /**
     * @return Model|Account
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findBranchSavingsAccountByOwnerId(string $ownerId): Account
    {
        return $this->getQuery()
            ->where('owner_id', $ownerId)
            ->firstOrFail();
    }

    /**
     * @return Model|Account
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findBranchOperationalAccountByOwnerId(string $ownerId): Account
    {
        return $this->getQuery()
            ->where('owner_id', $ownerId)
            ->firstOrFail();
    }

    public function findByName(string $name): ?Account
    {
        return $this->getQuery()
            ->where('name', $name)
            ->first();
    }

    public function getDefaultAccountByBranchAndType(string $branchId, string $transactionType): ?Account
    {
        return \DB::table(self::DEFAULT_ACCOUNT_TABLE)
            ->where('branch_id', $branchId)
            ->where('type', $transactionType)
            ->first();
    }

    public function getDefaultTransferTypesForBranchAndAccount(string $branchId, string $accountId): array
    {
        return DB::table(self::DEFAULT_ACCOUNT_TABLE)
            ->where('branch_id', $branchId)
            ->where('account_id', $accountId)
            ->pluck('transfer_type')
            ->toArray();
    }

    public function setDefaultAccount(string $branchId, string $transactionType, string $accountId): void
    {
        DB::table(self::DEFAULT_ACCOUNT_TABLE)
            ->updateOrInsert(
                ['branch_id' => $branchId, 'transfer_type' => $transactionType],
                ['account_id' => $accountId]
            );
    }
}