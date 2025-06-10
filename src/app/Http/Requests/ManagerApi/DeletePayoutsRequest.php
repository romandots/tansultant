<?php

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Payout\UpdateBatchDto;
use App\Models\Payout;
use Illuminate\Validation\Rule;

class DeletePayoutsRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return \array_merge(parent::rules(), [
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

    public function getDto(): UpdateBatchDto
    {
        $validated = $this->validated();
        $dto = new UpdateBatchDto($this->user());

        $dto->ids = $validated['ids'] ?? [];

        return $dto;
    }
}
