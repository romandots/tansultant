<?php
/**
 * File: LessonsOnDateRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Course;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class LessonsOnDateRequest
 * @package App\Http\Requests\Api
 */
class LessonsFilteredRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'date' => [
                'required',
                'date'
            ],
            'branch_id' => [
                'nullable',
                'string',
                'uuid',
                 Rule::exists(Branch::TABLE, 'id')
            ],
            'classroom_id' => [
                'nullable',
                'string',
                'uuid',
                 Rule::exists(Classroom::TABLE, 'id')
            ],
            'course_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Course::TABLE, 'id')
            ],
        ];
    }

    /**
     * @return DTO\LessonsFiltered
     */
    public function getDto(): DTO\LessonsFiltered
    {
        $validated = $this->validated();

        $date = Carbon::parse($validated['date']);
        if (!$date->isValid()) {
            throw new InvalidDateException('date', $date);
        }

        $dto = new DTO\LessonsFiltered;
        $dto->date = $date;
        $dto->branch_id = $validated['branch_id'] ?? null;
        $dto->classroom_id = $validated['classroom_id'] ?? null;
        $dto->course_id = $validated['course_id'] ?? null;

        return $dto;
    }
}
