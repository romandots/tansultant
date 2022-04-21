<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Contract\Dto;
use App\Models\Branch;
use App\Models\Student;
use Illuminate\Validation\Rule;

class StoreContractRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'serial' => [
                'nullable',
                'string',
            ],
            'branch_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Branch::TABLE, 'id'),
            ],
            'student_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Student::TABLE, 'id'),
            ],
        ];
    }

    public function getDto(): \App\Common\Contracts\DtoWithUser
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->serial = $validated['serial'] ?? null;
        $dto->branch_id = $validated['branch_id'];
        $dto->student_id = $validated['student_id'];

        return $dto;
    }
}