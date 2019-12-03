<?php
/**
 * File: FilterClassroomRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-08-1
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\PublicApi;

use App\Models\Branch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class FilterClassroomRequest
 * @package App\Http\Controllers\PublicApi
 */
class FilterClassroomRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists(Branch::TABLE, 'id')
            ]
        ];
    }

    /**
     * @return DTO\FilterClassroom
     */
    public function getDto(): DTO\FilterClassroom
    {
        $dto = new DTO\FilterClassroom;
        $validated = $this->validated();
        $dto->branch_id = isset($validated['branch_id']) ? (int)$validated['branch_id'] : null;

        return $dto;
    }
}
