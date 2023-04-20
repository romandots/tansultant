<?php

namespace App\Components\Account;

use App\Common\Contracts;
use App\Components\Account\Exceptions\AccountAlreadyExists;
use App\Components\Loader;
use App\Models\{Account, Branch, Enum\AccountType, Enum\TransactionStatus};
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
        if (!$dto->name) {
            $branch = Loader::branches()->findById($dto->branch_id);
            $dto->name = $this->generateAccountName($branch);
        }

        if ($existingAccount = $this->getRepository()->findByName($dto->name)) {
            throw new AccountAlreadyExists($existingAccount);
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
     * @param object $branch
     * @return Account
     * @throws \Exception
     */
    public function createBranchSavingsAccount($branch): Account
    {
        $name = \trans('account.name_presets.branch_savings', ['branch' => $branch->name]);
        $type = AccountType::SAVINGS;
        $ownerId = $branch->id;

        return $this->create($this->buildDto($name, $type, $ownerId));
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
        $ownerId = $branch->id;

        return $this->create($this->buildDto($name, $type, $ownerId));
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
        $availableAmount = $this->getAmount($account);
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

    private function generateAccountName(Branch $branch): string
    {
        return \trans('account.default_name_preset', ['branch' => $branch->name]);
    }

    private function buildDto(?string $name, AccountType $type, string $branchId): Dto
    {
        $dto = new Dto();
        $dto->name = $name;
        $dto->type = $type;
        $dto->branch_id = $branchId;

        return $dto;
    }
}