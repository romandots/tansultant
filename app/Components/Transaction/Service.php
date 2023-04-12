<?php

declare(strict_types=1);

namespace App\Components\Transaction;

use App\Common\Contracts;
use App\Components\Loader;
use App\Components\Shift\Exceptions\UserHasNoActiveShift;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\Enum\TransactionStatus;
use App\Models\Transaction;
use App\Services\Permissions\TransactionsPermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    protected \App\Components\Account\Facade $accounts;

    public function __construct()
    {
        parent::__construct(
            Transaction::class,
            Repository::class,
            Dto::class,
            null
        );
        $this->accounts = \app(\App\Components\Account\Facade::class);
    }

    /**
     * Creates manual transaction
     * @param Dto $dto
     * @return Transaction
     * @throws \Throwable
     */
    public function create(Contracts\DtoWithUser $dto): Model
    {
        $user = $dto->getUser();
        if (null === $user) {
            throw new \LogicException('user_is_not_defined');
        }

        $shift = $user?->load('active_shift')->active_shift;
        if (null === $shift && !$user->can(TransactionsPermission::CREATE_WITHOUT_SHIFT)) {
            throw new UserHasNoActiveShift($user);
        }

        $customer = $dto->customer_id ? Loader::customers()->findById($dto->customer_id) : null;

        $dto->shift_id = $shift?->id;
        $dto->name = $customer ? $this->generateDepositName($customer) : $dto->name;

        DB::beginTransaction();
        try {
            /** @var Transaction $transaction */
            $transaction = parent::create($dto);

            if ($customer) {
                $this->createCredit($transaction);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $transaction;
    }

    protected function createCredit(Transaction $transaction): Credit
    {
        return Loader::credits()->createIncome(
            $transaction->customer, $transaction->amount, $transaction->name, $transaction->user, $transaction
        );
    }

    protected function generateDepositName(Customer $customer): string
    {
        return trans('credit.deposit', ['customer' => $customer->name]);
    }

    public function createPayoutTransaction(
        \App\Models\Payout $payout,
        \App\Models\Account $account,
        \App\Models\User $user
    ): Transaction {
        $dto = new Dto($user);
        $dto->amount = 0 - (int)$payout->amount;
        $dto->account_id = $account->id;
        $dto->name = $payout->name;
        $dto->user_id = $user->id;
        $dto->status = TransactionStatus::CONFIRMED;
        $dto->type = \App\Models\Enum\TransactionType::AUTO;
        $dto->transfer_type = \App\Models\Enum\TransactionTransferType::CASH;
        $dto->confirmed_at = now();

        return parent::create($dto);
    }

}