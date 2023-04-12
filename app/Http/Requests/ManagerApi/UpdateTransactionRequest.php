<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Transaction\Dto;
use App\Models\Account;
use App\Models\Enum\TransactionTransferType;
use Illuminate\Validation\Rule;

class UpdateTransactionRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'name' => [
                'required',
                'string',
            ],
            'account_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Account::TABLE, 'id'),
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

        $dto->name = $validated['name'];
        $dto->account_id = $validated['account_id'];
        $dto->amount = (int)$validated['amount'];
        $dto->transfer_type = TransactionTransferType::tryFrom($validated['transfer_type']);

        return $dto;
    }

}