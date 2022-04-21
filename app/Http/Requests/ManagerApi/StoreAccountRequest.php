<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Account\Dto;
use App\Models\Enum\{AccountOwnerType, AccountType};
use Illuminate\Validation\Rule;

class StoreAccountRequest extends StoreRequest
{

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'string',
                Rule::in(AccountType::cases()),
            ],
            'owner_type' => [
                'required',
                'string',
                Rule::in(AccountOwnerType::cases()),
            ],
            'owner_id' => [
                'required',
                'string',
                'uuid',
            ],
        ];
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->type = AccountType::from($validated['type']);
        $dto->owner_type = AccountOwnerType::from($validated['owner_type']);
        $dto->owner_id = $validated['owner_id'];

        return $dto;
    }
}