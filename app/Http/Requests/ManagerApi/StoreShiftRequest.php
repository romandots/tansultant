<?php
/**
 * File: StoreVisitRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Shift\Dto;
use Illuminate\Validation\Rule;

class
StoreShiftRequest extends StoreRequest
{
    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();
        /** @var Dto $dto */
        $dto = parent::getDto();
        $dto->branch_id = $validated['branch_id'] ?? null;

        return $dto;
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'branch_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists('branches', 'id'),
            ],
        ]);
    }

}
