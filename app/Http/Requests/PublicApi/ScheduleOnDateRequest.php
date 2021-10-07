<?php
/**
 * File: ScheduleOnDateRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-3
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\PublicApi;

use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Schedule;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ScheduleOnDateRequest
 * @package App\Http\Requests\Api
 */
class ScheduleOnDateRequest extends FormRequest
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
     * @return \App\Http\Requests\ManagerApi\DTO\ScheduleOnDate
     */
    public function getDto(): \App\Http\Requests\ManagerApi\DTO\ScheduleOnDate
    {
        $validated = $this->validated();

        $date = Carbon::parse($validated['date']);
        if (!$date->isValid()) {
            throw new InvalidDateException('date', $date);
        }

        $weekday = \mb_strtolower(\weekday($date));

        if (!\in_array($weekday, Schedule::WEEKDAYS, true)) {
            throw new \LogicException('Weekday is unusual', ['weekday' => $weekday]);
        }

        $dto = new \App\Http\Requests\ManagerApi\DTO\ScheduleOnDate;
        $dto->date = $date;
        $dto->weekday = $weekday;
        $dto->branch_id = $validated['branch_id'] ?? null;
        $dto->classroom_id = $validated['classroom_id'] ?? null;
        $dto->course_id = $validated['course_id'] ?? null;

        return $dto;
    }
}
