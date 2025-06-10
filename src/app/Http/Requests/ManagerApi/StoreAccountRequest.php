<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Account\Dto;
use App\Models\Account;
use App\Models\Branch;
use Illuminate\Validation\Rule;

class StoreAccountRequest extends StoreRequest
{

    public function rules(): array
    {
        return \array_merge(parent::rules(), [
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
            'external_id' => [
                'nullable',
                'string',
            ],
            'external_system' => [
                'nullable',
                'string',
            ],
        ]);
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->name = $validated['name'] ?? null;
        $dto->branch_id = $validated['branch_id'];
        $dto->external_id = $validated['external_id'] ?? null;
        $dto->external_system = $validated['external_system'] ?? null;

        return $dto;
    }
}