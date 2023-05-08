<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Loader;
use App\Components\Transaction\Dto;
use App\Components\Transaction\Exceptions\AccountCannotBeSelectedException;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Enum\TransactionStatus;
use App\Models\Enum\TransactionTransferType;
use App\Models\Enum\TransactionType;
use App\Models\User;
use App\Services\Permissions\AccountsPermission;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'name' => [
                'required_without:customer_id',
                'string',
            ],
            'account_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Account::TABLE, 'id'),
            ],
            'customer_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Customer::TABLE, 'id'),
            ],
            'amount' => [
                'required',
                'integer',
            ],
            'transfer_type' => [
                'required',
                'string',
                Rule::in([TransactionTransferType::CARD->value, TransactionTransferType::CASH->value]),
            ],
        ]);
    }

    /**
     * @return Dto
     * @throws AccountCannotBeSelectedException
     */
    public function getDto(): Dto
    {
        $validated = $this->validated();
        /** @var User $user */
        $user = $this->user();
        $dto = new Dto($user);

        $dto->name = $validated['name'] ?? null;
        $dto->account_id = $this->getAccountId($user, $validated);
        $dto->customer_id = $validated['customer_id'] ?? null;
        $dto->amount = (int)$validated['amount'];
        $dto->transfer_type = TransactionTransferType::tryFrom($validated['transfer_type']);
        $dto->type = TransactionType::MANUAL;
        $dto->status = TransactionStatus::CONFIRMED;
        $dto->confirmed_at = now();
        $dto->user_id = $this->user()->id;

        return $dto;
    }

    /**
     * @param User $user
     * @param array $validated
     * @return string
     * @throws AccountCannotBeSelectedException
     */
    protected function getAccountId(User $user, array $validated): string
    {
        return match (true) {
            $user->canAny([AccountsPermission::MANAGE, AccountsPermission::READ]) => $validated['account_id'],
            $user->active_shift_id !== null =>
            $this->getAccountIdByTransferTypeForBranch(
                TransactionTransferType::tryFrom($validated['transfer_type']),
                $user->active_shift->branch
            ),
            default => throw new AccountCannotBeSelectedException(),
        };
    }

    protected function getAccountIdByTransferTypeForBranch(
        TransactionTransferType $transferType,
        \App\Models\Branch $branch
    ): string {
        $account = Loader::accounts()->getDefaultBranchAccount($branch, $transferType);
        if ($account !== null) {
            return $account->id;
        }

        throw new AccountCannotBeSelectedException();
    }
}