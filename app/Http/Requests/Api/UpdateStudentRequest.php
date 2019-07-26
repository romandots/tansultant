<?php
/**
 * File: UpdateStudentRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateStudentRequest
 * @package App\Http\Requests\Api
 */
class UpdateStudentRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'card_number' => [
                'nullable',
                'integer',
                Rule::unique(Student::TABLE)
            ],
        ];
    }

    /**
     * @return DTO\Student
     */
    public function getDto(): DTO\Student
    {
        $validated = $this->validated();
        $dto = new DTO\Student;
        $dto->card_number = $validated['card_number'] ?? null;

        return $dto;
    }
}
