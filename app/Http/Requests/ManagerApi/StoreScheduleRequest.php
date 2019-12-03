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
                'nullable',
                'date'
            ],
            'ends_at' => [
                'nullable',
                'date'
            ],
            'duration' => [
                'required',
                'integer',
                'min:15'
            ],
            'monday' => [
                'nullable',
                'date_format:H:i'
            ],
            'tuesday' => [
                'nullable',
                'date_format:H:i'
            ],
            'wednesday' => [
                'nullable',
                'date_format:H:i'
            ],
            'thursday' => [
                'nullable',
                'date_format:H:i'
            ],
            'friday' => [
                'nullable',
                'date_format:H:i'
            ],
            'saturday' => [
                'nullable',
                'date_format:H:i'
            ],
            'sunday' => [
                'nullable',
                'date_format:H:i'
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
        $dto->starts_at = isset($validated['starts_at']) ? Carbon::parse($validated['starts_at']) : null;
        $dto->ends_at = isset($validated['ends_at']) ? Carbon::parse($validated['ends_at']) : null;
        $dto->duration = (int)$validated['duration'];
        $dto->monday = isset($validated['monday']) ? Carbon::createFromFormat('H:i', $validated['monday']) : null;
        $dto->tuesday = isset($validated['tuesday']) ? Carbon::createFromFormat('H:i', $validated['tuesday']) : null;
        $dto->wednesday = isset($validated['wednesday']) ? Carbon::createFromFormat('H:i',
            $validated['wednesday']) : null;
        $dto->thursday = isset($validated['thursday']) ? Carbon::createFromFormat('H:i', $validated['thursday']) : null;
        $dto->friday = isset($validated['friday']) ? Carbon::createFromFormat('H:i', $validated['friday']) : null;
        $dto->saturday = isset($validated['saturday']) ? Carbon::createFromFormat('H:i', $validated['saturday']) :
            null;
        $dto->sunday = isset($validated['sunday']) ? Carbon::createFromFormat('H:i', $validated['sunday']) : null;

        return $dto;
    }
}
