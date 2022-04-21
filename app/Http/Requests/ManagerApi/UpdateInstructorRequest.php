<?php
/**
 * File: AttachInstructorRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Instructor\Dto;
use App\Models\Enum\InstructorStatus;
use Illuminate\Validation\Rule;

class UpdateInstructorRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'description' => [
                'nullable',
                'string'
            ],
            'status' => [
                'required',
                'string',
                Rule::in(InstructorStatus::cases())
            ],
            'display' => [
                'nullable',
                'boolean'
            ],
        ];
    }

    public function getDto(): Dto
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->name = $validated['name'] ?? null;
        $dto->description = $validated['description'] ?? null;
        $dto->status = InstructorStatus::from($validated['status']);
        $dto->display = (bool)($validated['display'] ?? null);

        return $dto;
    }
}
