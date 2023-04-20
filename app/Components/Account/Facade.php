<?php

declare(strict_types=1);

namespace App\Components\Account;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Models\Account;
use App\Models\Branch;
use App\Models\Enum\TransactionTransferType;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Account> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Account> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Account create(Dto $dto, array $relations = [])
 * @method \App\Models\Account find(ShowDto $showDto)
 * @method \App\Models\Account findById(string $id)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Account findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Account findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    /**
     * @param Account $account
     * @param int $amount
     * @throws Exceptions\InsufficientFundsAccountException
     */
    public function checkFunds(Account $account, int $amount): void
    {
        $this->getService()->checkFunds($account, $amount);
    }

    public function getAmount(Account $account): int
    {
        return $this->getService()->getAmount($account);
    }

    public function getDefaultBranchAccount(Branch $branch, TransactionTransferType $transactionTransferType): ?Account
    {
        return $this->getRepository()->getDefaultAccountByBranchAndType($branch->id, $transactionTransferType->value);
    }

    /**
     * @param Account $account
     * @return TransactionTransferType[]
     */
    public function getTransferTypesAccountIsDefaultFor(Account $account): array
    {
        $transferTypes = $this->getRepository()->getDefaultTransferTypesForBranchAndAccount($account->branch_id, $account->id);
        return array_filter(array_map(
            static fn (string $transferType) => TransactionTransferType::tryFrom($transferType),
            $transferTypes ?? []
        ));
    }

    public function setDefaultBranchAccount(
        Branch $branch,
        TransactionTransferType $transactionTransferType,
        Account $account
    ): void {
        $this->getRepository()->setDefaultAccount($branch->id, $transactionTransferType->value, $account->id);
    }

    public function setDefaultForTransactionTransferType(Account $account, TransactionTransferType $transactionTransferType): void
    {
        $this->setDefaultBranchAccount($account->branch, $transactionTransferType, $account);
    }
}