<?php

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Price\Dto;
use App\Models\Price;
use Illuminate\Validation\Rule;

class StorePriceRequest extends StoreRequest
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
                Rule::unique(Price::TABLE, 'name')->ignore($this->getId()),
            ],
            'price' => [
                'required',
                'int',
                'min:0',
            ],
            'special_price' => [
                'nullable',
                'int',
                'min:0',
            ],
        ]);
    }

    public function getDto(): Dto
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->name = $validated['name'];
        $dto->price = $validated['price'];
        $dto->special_price = $validated['special_price'];

        return $dto;
    }

}
