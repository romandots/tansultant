<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Account\Dto;
use App\Models\Account;
use App\Models\Branch;
use App\Models\Enum\{AccountType};
use Illuminate\Validation\Rule;

class StoreAccountRequest extends StoreRequest
{

    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'type' => [
                'required',
                'string',
                Rule::in(enum_strings(AccountType::class)),
            ],
            'name' => [
                'nullable',
                'string',
                Rule::unique(Account::TABLE, 'name'),
            ],
            'branch_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Branch::TABLE, 'id'),
            ],
        ]);
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->type = AccountType::from($validated['type']);
        $dto->name = $validated['name'] ?? null;
        $dto->branch_id = $validated['branch_id'];

        return $dto;
    }
}