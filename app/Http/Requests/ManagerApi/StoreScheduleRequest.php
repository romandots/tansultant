<?php
/**
 * File: .php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreScheduleRequest
 * @package App\Http\Requests\Api
 */
class StoreScheduleRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'branch_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Branch::TABLE, 'id'),
            ],
            'classroom_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Classroom::TABLE, 'id'),
            ],
            'course_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Course::TABLE, 'id'),
            ],
            'starts_at' => [
                'required',
                'regex:/\d{1,2}:\d{1,2}(:\d{1,2})?.+$/i',
            ],
            'ends_at' => [
                'required',
                'regex:/\d{1,2}:\d{1,2}(:\d{1,2})?.+$/i',
            ],
            'weekday' => [
                'required',
                'string',
                Rule::in(Schedule::WEEKDAYS),
            ],
        ];
    }

    /**
     * @return DTO\StoreSchedule
     */
    public function getDto(): DTO\StoreSchedule
    {
        $validated = $this->validated();

        $dto = new DTO\StoreSchedule;
        $dto->branch_id = $validated['branch_id'];
        $dto->classroom_id = $validated['classroom_id'];
        $dto->course_id = $validated['course_id'];
        $dto->weekday = $validated['weekday'];
        $dto->starts_at = isset($validated['starts_at']) ? Carbon::parse($validated['starts_at']) : null;
        $dto->ends_at = isset($validated['ends_at']) ? Carbon::parse($validated['ends_at']) : null;

        return $dto;
    }
}
