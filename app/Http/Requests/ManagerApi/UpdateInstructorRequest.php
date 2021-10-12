<?php
/**
 * File: UpdateInstructorRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInstructorRequest extends FormRequest
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
                Rule::in(\App\Models\Instructor::STATUSES)
            ],
            'display' => [
                'nullable',
                'boolean'
            ],
        ];
    }

    /**
     * @return \App\Http\Requests\DTO\StoreInstructor
     */
    public function getDto(): \App\Http\Requests\DTO\StoreInstructor
    {
        $validated = $this->validated();

        $dto = new \App\Http\Requests\DTO\StoreInstructor;
        $dto->description = $validated['description'] ?? null;
        $dto->status = $validated['status'];
        $dto->display = (bool)$validated['display'];

        return $dto;
    }
}
