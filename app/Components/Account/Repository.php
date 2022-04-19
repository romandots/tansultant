<?php

declare(strict_types=1);

namespace App\Components\Account;

use App\Models\{Account, Enum\AccountOwnerType, Enum\AccountType, Instructor, Student};
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Account make()
 * @method int countFiltered(\App\Common\Contracts\FilteredInterface $search)
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

    /**
     * @param Student $student
     * @return Account
     * @throws \Exception
     */
    public function createStudentPersonalAccount(Student $student): Account
    {
        $name = \trans('account.name_presets.student', ['student' => $student->name]);
        $type = AccountType::PERSONAL;
        $ownerType = AccountOwnerType::STUDENT;
        $ownerId = $student->id;

        return $this->create($this->buildDto($name, $type, $ownerType, $ownerId));
    }

    /**
     * @param Instructor $instructor
     * @return Account
     * @throws \Exception
     */
    public function createInstructorPersonalAccount(Instructor $instructor): Account
    {
        $name = \trans('account.name_presets.instructor', ['instructor' => $instructor->name]);
        $type = AccountType::PERSONAL;
        $ownerType = AccountOwnerType::INSTRUCTOR;
        $ownerId = $instructor->id;

        return $this->create($this->buildDto($name, $type, $ownerType, $ownerId));
    }

    /**
     * @param object $branch
     * @return Account
     * @throws \Exception
     */
    public function createBranchSavingsAccount($branch): Account
    {
        $name = \trans('account.name_presets.branch_savings', ['branch' => $branch->name]);
        $type = AccountType::SAVINGS;
        $ownerType = AccountOwnerType::BRANCH;
        $ownerId = $branch->id;

        return $this->create($this->buildDto($name, $type, $ownerType, $ownerId));
    }

    /**
     * @param object $branch
     * @return Account
     * @throws \Exception
     */
    public function createBranchOperationalAccount($branch): Account
    {
        $name = \trans('account.name_presets.branch_operational', ['branch' => $branch->name]);
        $type = AccountType::OPERATIONAL;
        $ownerType = AccountOwnerType::BRANCH;
        $ownerId = $branch->id;

        return $this->create($this->buildDto($name, $type, $ownerType, $ownerId));
    }

    private function buildDto(?string $name, AccountType $type, AccountOwnerType $ownerType, string $ownerId): Dto
    {
        $dto = new Dto();
        $dto->name = $name;
        $dto->type = $type;
        $dto->owner_type = $ownerType;
        $dto->owner_id = $ownerId;

        return $dto;
    }
}