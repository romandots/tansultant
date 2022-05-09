<?php

declare(strict_types=1);

namespace App\Components\Account;

use App\Models\{Account, Enum\AccountOwnerType, Enum\AccountType};
use Illuminate\Database\Eloquent\Model;

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
        $record->type = $dto->type->value;
        $record->owner_type = $dto->owner_type->value;
        $record->owner_id = $dto->owner_id;
    }

    /**
     * @param string $ownerId
     * @return Account
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findInstructorPersonalAccountByOwnerId(string $ownerId): Account
    {
        return $this->getQuery()
            ->where('type', AccountType::PERSONAL)
            ->where('owner_type', AccountOwnerType::INSTRUCTOR)
            ->where('owner_id', $ownerId)
            ->firstOrFail();
    }

    /**
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findStudentPersonalAccountByOwnerId(string $ownerId): Account
    {
        return $this->getQuery()
            ->where('type', AccountType::PERSONAL)
            ->where('owner_type', AccountOwnerType::STUDENT)
            ->where('owner_id', $ownerId)
            ->firstOrFail();
    }

    /**
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findBranchSavingsAccountByOwnerId(string $ownerId): Account
    {
        return $this->getQuery()
            ->where('type', AccountType::SAVINGS)
            ->where('owner_type', AccountOwnerType::BRANCH)
            ->where('owner_id', $ownerId)
            ->firstOrFail();
    }

    /**
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findBranchOperationalAccountByOwnerId(string $ownerId): Account
    {
        return $this->getQuery()
            ->where('type', AccountType::OPERATIONAL)
            ->where('owner_type', AccountOwnerType::BRANCH)
            ->where('owner_id', $ownerId)
            ->firstOrFail();
    }
}