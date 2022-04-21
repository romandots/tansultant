<?php
/**
 * File: StoreClassroomRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Classroom\Dto;
use App\Models\Branch;
use Illuminate\Validation\Rule;

/**
 * Class StoreClassroomRequest
 * @package App\Http\Requests\PublicApi
 */
class StoreClassroomRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
            ],
            'branch_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Branch::TABLE, 'id')
            ],
            'color' => [
                'nullable',
                'string',
            ],
            'capacity' => [
                'nullable',
                'integer',
                'min:1',
                'max:150'
            ],
            'number' => [
                'nullable',
                'integer',
            ],
        ];
    }

    public function getDto(): Dto
    {
        $valid = $this->validated();

        $dto = new Dto($this->user());
        $dto->name = $valid['name'];
        $dto->branch_id = $valid['branch_id'];
        $dto->color = $valid['color'] ?? null;
        $dto->capacity = $valid['capacity'] ?? null;
        $dto->number = $valid['number'] ?? null;

        return $dto;
    }
}
