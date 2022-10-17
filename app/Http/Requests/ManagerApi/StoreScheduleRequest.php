<?php
/**
 * File: .php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\DTO\DtoWithUser;
use App\Common\Requests\StoreRequest;
use App\Components\Loader;
use App\Components\Schedule\Dto;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Enum\ScheduleCycle;
use App\Models\Enum\Weekday;
use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Class StoreScheduleRequest
 * @package App\Http\Requests\Api
 */
class StoreScheduleRequest extends StoreRequest
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return \array_merge(parent::rules(), [
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
            'price_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Price::TABLE, 'id'),
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
        ]);
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

        $classroom = Loader::classrooms()->find($validated['classroom_id']);

        $dto = new Dto($this->user());
        $dto->branch_id = $classroom->branch_id;
        $dto->classroom_id = $classroom->id;
        $dto->course_id = $validated['course_id'];
        $dto->price_id = $validated['price_id'] ?? null;
        $dto->cycle = ScheduleCycle::from($validated['cycle']);
        $dto->weekday = isset($validated['weekday']) ? Weekday::from((int)$validated['weekday']) : null;
        $dto->from_date = isset($validated['from_date']) ? Carbon::parse($validated['from_date']) : null;
        $dto->to_date = isset($validated['to_date']) ? Carbon::parse($validated['to_date']) : null;
        $dto->starts_at = Carbon::parse($validated['starts_at']);
        $dto->ends_at = Carbon::parse($validated['ends_at']);

        return $dto;
    }
}
