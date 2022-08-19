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
use App\Models\Person;
use Illuminate\Validation\Rule;

/**
 * Class AttachInstructorRequest
 * @property-read string $person_id
 * @package App\Http\Requests\Api
 */
class StoreInstructorRequest extends StoreRequest
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
                Rule::in(enum_strings(\App\Models\Enum\InstructorStatus::class)),
            ],
            'display' => [
                'nullable',
                'boolean'
            ],
            'person_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Person::TABLE, 'id'),
                //Rule::unique(Instructor::TABLE, 'person_id')->ignore($this->getInstructorId()), // Handled by Servicea
            ],
        ];
    }

    public function getDto(): Dto
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        //$dto->name = $validated['name'] ?? null;
        $dto->description = $validated['description'] ?? null;
        $dto->status = InstructorStatus::from($validated['status']);
        $dto->display = (bool)($validated['display'] ?? false);
        $dto->person_id = $validated['person_id'];

        return $dto;
    }

    protected function getInstructorId(): ?string
    {
        return $this->route()->parameter('id');
    }
}
