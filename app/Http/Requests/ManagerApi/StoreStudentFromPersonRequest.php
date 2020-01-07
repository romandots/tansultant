<?php
/**
 * File: StoreStudentFromPersonRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-7
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Models\Person;
use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreStudentRequest
 * @package App\Http\Requests\Api
 */
class StoreStudentFromPersonRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'card_number' => [
                'nullable',
                'string',
                Rule::unique(Student::TABLE)
            ],
            'person_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Person::TABLE, 'id')
            ],
        ];
    }

    public function getPersonDto(): \App\Http\Requests\DTO\StoreStudentFromPerson
    {
        $validated = $this->validated();
        $dto = new \App\Http\Requests\DTO\StoreStudentFromPerson();
        $dto->person_id = $validated['person_id'];

        return $dto;
    }

    public function getStudentDto(): \App\Http\Requests\DTO\StoreStudent
    {
        $validated = $this->validated();
        $dto = new \App\Http\Requests\DTO\StoreStudent();
        $dto->card_number = $validated['card_number'] ?? null;

        return $dto;
    }
}
