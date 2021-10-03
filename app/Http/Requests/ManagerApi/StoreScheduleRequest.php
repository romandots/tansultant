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
use App\Repository\ClassroomRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Class StoreScheduleRequest
 * @package App\Http\Requests\Api
 */
class StoreScheduleRequest extends FormRequest
{
    protected ClassroomRepository $classroomRepository;

    /**
     * StoreScheduleRequest constructor.
     * @param ClassroomRepository $classroomRepository
     */
    public function __construct(ClassroomRepository $classroomRepository)
    {
        parent::__construct();
        $this->classroomRepository = $classroomRepository;
    }

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

    /**
     * @return DTO\StoreSchedule
     */
    public function getDto(): DTO\StoreSchedule
    {
        $validated = $this->validated();

        $classroom = isset($validated['classroom_id'])
            ? $this->classroomRepository->find($validated['classroom_id'])
            : null;
        $branchId = $validated['branch_id'] ?? null;

        $dto = new DTO\StoreSchedule;
        $dto->branch_id = null !== $classroom ? $classroom->branch_id : $branchId;
        $dto->classroom_id = null !== $classroom ? $classroom->id : null;
        $dto->course_id = $this->getCourseId();
        $dto->cycle = $validated['cycle'];
        $dto->weekday = $validated['weekday'] ?? null;
        $dto->from_date = isset($validated['from_date']) ? Carbon::parse($validated['from_date']) : null;
        $dto->to_date = isset($validated['to_date']) ? Carbon::parse($validated['to_date']) : null;
        $dto->starts_at = Carbon::parse($validated['starts_at']);
        $dto->ends_at = Carbon::parse($validated['ends_at']);
        $dto->user = $this->user();

        return $dto;
    }
}
