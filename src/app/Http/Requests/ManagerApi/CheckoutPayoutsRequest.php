<?php

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Payout\CheckoutBatchDto;
use App\Models\Account;
use App\Models\Payout;
use Illuminate\Validation\Rule;

class CheckoutPayoutsRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'account_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Account::TABLE, 'id'),
            ],
            'ids' => [
                'required',
                'array',
            ],
            'ids.*' => [
                'required_with:ids',
                'string',
                'uuid',
                Rule::exists(Payout::TABLE, 'id'),
            ],
        ]);
    }

    public function getDto(): CheckoutBatchDto
    {
        $validated = $this->validated();
        $dto = new CheckoutBatchDto($this->user());

        $dto->ids = $validated['ids'] ?? [];
        $dto->account_id = $validated['account_id'];

        return $dto;
    }
}
