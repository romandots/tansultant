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
    private function getCourseId(): string
    {
        return (string)$this->route()->parameter('courseId');
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'branch_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Branch::TABLE, 'id'),
            ],
            'classroom_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Classroom::TABLE, 'id'),
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
        $dto->branch_id = $validated['branch_id'] ?? null;
        $dto->classroom_id = $validated['classroom_id'] ?? null;
        $dto->course_id = $this->getCourseId();
        $dto->weekday = $validated['weekday'];
        $dto->starts_at = Carbon::parse($validated['starts_at']);
        $dto->ends_at = Carbon::parse($validated['ends_at']);
        $dto->user = $this->user();

        return $dto;
    }
}
