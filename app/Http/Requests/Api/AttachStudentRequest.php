<?php
/**
 * File: AttachStudentRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Person;
use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class AttachStudentRequest
 * @package App\Http\Controllers\Api
 */
class AttachStudentRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'card_number' => [
                'required',
                'string',
                Rule::unique(Student::TABLE)->ignore($this->input('person_id'), 'person_id')
            ],
            'person_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Person::TABLE, 'id')
            ]
        ];
    }

    /**
     * @return DTO\StudentFromPerson
     */
    public function getDto(): DTO\StudentFromPerson
    {
        $validated = $this->validated();

        $dto = new DTO\StudentFromPerson;
        $dto->card_number = $validated['card_number'];
        $dto->person_id = $validated['person_id'];

        return $dto;
    }
}
