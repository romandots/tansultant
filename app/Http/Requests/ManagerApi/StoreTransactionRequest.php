<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Transaction\Dto;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Enum\TransactionStatus;
use App\Models\Enum\TransactionTransferType;
use App\Models\Enum\TransactionType;
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
                'required_if_null:customer_id',
                'string',
            ],
            'account_id' => [
                'required',
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

    public function getDto(): Dto
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->name = $validated['name'] ?? null;
        $dto->account_id = $validated['account_id'];
        $dto->customer_id = $validated['customer_id'] ?? null;
        $dto->amount = (int)$validated['amount'];
        $dto->transfer_type = TransactionTransferType::tryFrom($validated['transfer_type']);
        $dto->type = TransactionType::MANUAL;
        $dto->status = TransactionStatus::CONFIRMED;
        $dto->confirmed_at = now();
        $dto->user_id = $this->user()->id;

        return $dto;
    }

}