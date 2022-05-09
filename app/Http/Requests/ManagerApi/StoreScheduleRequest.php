<?php
/**
 * File: .php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Contracts\DtoWithUser;
use App\Common\Requests\StoreRequest;
use App\Components\Loader;
use App\Components\Schedule\Dto;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Enum\ScheduleCycle;
use App\Models\Enum\Weekday;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Class StoreScheduleRequest
 * @package App\Http\Requests\Api
 */
class StoreScheduleRequest extends StoreRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->classroomRepository = Loader::classrooms()->getRepository();
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'course_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Course::TABLE, 'id'),
            ],
            'classroom_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Classroom::TABLE, 'id'),
            ],
            'from_date' => [
                'required_unless:cycle,' . ScheduleCycle::EVERY_WEEK->value,
                'regex:/\d{4}-\d{1,2}-\d{1,2}$/i',
            ],
            'to_date' => [
                'nullable',
                'regex:/\d{4}-\d{1,2}-\d{1,2}$/i',
            ],
            'starts_at' => [
                'required',
                'regex:/\d{1,2}:\d{1,2}(:\d{1,2})?.+$/i',
            ],
            'ends_at' => [
                'required',
                'regex:/\d{1,2}:\d{1,2}(:\d{1,2})?.+$/i',
            ],
            'cycle' => [
                'required',
                'string',
                Rule::in(enum_strings(ScheduleCycle::class)),
            ],
            'weekday' => [
                'nullable',
                'required_if:cycle,' . ScheduleCycle::EVERY_WEEK->value,
                'int',
                Rule::in(enum_strings(Weekday::class)),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(static function (Validator $validator) {
            $data = $validator->getData();
            if (isset($data['starts_at'], $data['ends_at']) &&
                Carbon::parse($data['starts_at'])->gt(Carbon::parse($data['ends_at']))) {
                    $validator->errors()->add( 'starts_at', 'invalid' );
                }
        });
    }

    public function getDto(): DtoWithUser
    {
        $validated = $this->validated();

        $classroom = isset($validated['classroom_id'])
            ? $this->classroomRepository->find($validated['classroom_id'])
            : null;
        $branchId = $validated['branch_id'] ?? null;

        $dto = new Dto($this->user());
        $dto->branch_id = null !== $classroom ? $classroom->branch_id : $branchId;
        $dto->classroom_id = $classroom?->id;
        $dto->course_id = $validated['course_id'];
        $dto->cycle = ScheduleCycle::from($validated['cycle']);
        $dto->weekday = isset($validated['weekday']) ? Weekday::from($validated['weekday']) : null;
        $dto->from_date = isset($validated['from_date']) ? Carbon::parse($validated['from_date']) : null;
        $dto->to_date = isset($validated['to_date']) ? Carbon::parse($validated['to_date']) : null;
        $dto->starts_at = Carbon::parse($validated['starts_at']);
        $dto->ends_at = Carbon::parse($validated['ends_at']);

        return $dto;
    }
}
