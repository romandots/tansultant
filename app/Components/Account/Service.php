<?php

namespace App\Components\Account;

use App\Common\Contracts;
use App\Models\{Account,
    Branch,
    Enum\AccountOwnerType,
    Enum\AccountType,
    Enum\BonusStatus,
    Enum\TransactionStatus,
    Instructor,
    Student};
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Account::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * @param Dto $dto
     * @return Account
     * @throws \Throwable
     */
    public function create(Contracts\DtoWithUser $dto): Model
    {
        if (!isset($dto->name)) {
            $dto->name = $this->generateAccountName($dto->type, $dto->owner_type);
        }
        return parent::create($dto);
    }

    /**
     * @param Account $record
     * @param Dto $dto
     * @return void
     * @throws \Throwable
     */
    public function update(Model $record, Contracts\DtoWithUser $dto): void
    {
        if (!isset($dto->name)) {
            $dto->name = $record->name;
        }
        parent::update($record, $dto);
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

    /**
     * @param Student $student
     * @return Account
     * @throws \Exception
     */
    public function getStudentAccount(Student $student): Account
    {
        try {
            return $this->getRepository()->findStudentPersonalAccountByOwnerId($student->id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->createStudentPersonalAccount($student);
        }
    }

    /**
     * @param Instructor $instructor
     * @return Account
     * @throws \Exception
     */
    public function getInstructorAccount(Instructor $instructor): Account
    {
        try {
            return $this->getRepository()->findInstructorPersonalAccountByOwnerId($instructor->id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->createInstructorPersonalAccount($instructor);
        }
    }

    /**
     * @param Branch $branch
     * @return Account
     * @throws \Exception
     */
    public function getOperationalAccount(Branch $branch): Account
    {
        try {
            return $this->getRepository()->findBranchOperationalAccountByOwnerId($branch->id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->createBranchOperationalAccount($branch);
        }
    }

    /**
     * @param Branch $branch
     * @return Account
     * @throws \Exception
     */
    public function getSavingsAccount(Branch $branch): Account
    {
        try {
            return $this->getRepository()->findBranchSavingsAccountByOwnerId($branch->id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->createBranchSavingsAccount($branch);
        }
    }

    /**
     * @param Account $account
     * @param int $amount
     * @throws Exceptions\InsufficientFundsAccountException
     */
    public function checkFunds(Account $account, int $amount): void
    {
        $availableAmount = $this->getTotalAmount($account);
        if ($availableAmount < $amount) {
            throw new Exceptions\InsufficientFundsAccountException($account, $availableAmount, $amount);
        }
    }

    /**
     * @param Account $account
     * @return int
     */
    public function getAmount(Account $account): int
    {
        return $account->load('payments')->payments
            ->where('status', TransactionStatus::CONFIRMED)
            ->sum('amount');
    }

    /**
     * @param Account $account
     * @return int
     */
    public function getBonusAmount(Account $account): int
    {
        return $account->load('bonuses')->bonuses
            ->where('status', BonusStatus::PENDING)
            ->sum('amount');
    }

    /**
     * @param Account $account
     * @return int
     */
    public function getTotalAmount(Account $account): int
    {
        return $this->getAmount($account) + $this->getBonusAmount($account);
    }

    private function generateAccountName(AccountType $type, AccountOwnerType $ownerType): string
    {
        $typeString = \trans('account.type.' . $type->value);
        $ownerTypeString = \trans('account.owner_type.' . $ownerType->value);

        return sprintf('%s %s', $typeString, $ownerTypeString);
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