<?php

declare(strict_types=1);

namespace App\Components\Credit;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Credit> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Credit> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Credit create(Dto $dto, array $relations = [])
 * @method \App\Models\Credit find(ShowDto $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Credit findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Credit findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function createWithdrawal(
        Customer $customer,
        int $amount,
        string $comment,
        User $user,
        ?Transaction $transaction = null
    ): Credit {
        return $this->getService()->createWithdrawal($customer, $amount, $comment, $user, $transaction);
    }

    public function createIncome(
        Customer $customer,
        int $amount,
        string $comment,
        User $user,
        ?Transaction $transaction = null
    ): Credit {
        return $this->getService()->createIncome($customer, $amount, $comment, $user, $transaction);
    }

    public function getCustomerCredits(Customer $customer): int
    {
        return $this->getRepository()->getCreditsSumByCustomerId($customer->id);
    }
}