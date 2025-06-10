<?php
/**
 * File: StoreFormulaRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Formula\Dto;
use App\Models\Subscription;
use Illuminate\Validation\Rule;

class StoreFormulaRequest extends StoreRequest
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
                Rule::unique(Subscription::TABLE, 'name')->ignore($this->getId()),
            ],
            'equation' => [
                'nullable',
                'string',
            ],
        ]);
    }

    public function getDto(): Dto
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->name = $validated['name'];
        $dto->equation = mb_strtoupper($validated['equation']);

        return $dto;
    }

}
