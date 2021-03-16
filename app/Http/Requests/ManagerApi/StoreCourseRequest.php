<?php
/**
 * File: StoreCourseRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Models\Course;
use App\Models\Instructor;
use App\Repository\InstructorRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreCourseRequest
 * @package App\Http\Requests\Api
 */
class StoreCourseRequest extends FormRequest
{
    private InstructorRepository $instructorRepository;

    /**
     * StoreCourseRequest constructor.
     * @param InstructorRepository $instructorRepository
     */
    public function __construct(InstructorRepository $instructorRepository)
    {
        parent::__construct();
        $this->instructorRepository = $instructorRepository;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string'
            ],
            'status' => [
                'nullable',
                'string',
                Rule::in(Course::STATUSES)
            ],
            'summary' => [
                'nullable',
                'string'
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'display' => [
                'nullable',
                'bool'
            ],
            'age_restrictions_from' => [
                'nullable',
                'int',
                'min:0',
                'max:100',
            ],
            'age_restrictions_to' => [
                'nullable',
                'int',
                'min:0',
                'max:100',
            ],
            'instructor_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Instructor::TABLE, 'id')
            ],
            'starts_at' => [
                'nullable',
                'date'
            ],
            'ends_at' => [
                'nullable',
                'date'
            ],
            'genres' => [
                'nullable',
                'array'
            ],
            'genres.*' => [
                'string',
            ],
        ];
    }

    /**
     * @return DTO\StoreCourse
     */
    public function getDto(): DTO\StoreCourse
    {
        $validated = $this->validated();

        $dto = new DTO\StoreCourse();
        $dto->name = $validated['name'];
        $dto->status = $validated['status'] ?? Course::STATUS_PENDING;
        $dto->summary = $validated['summary'] ?? null;
        $dto->description = $validated['description'] ?? null;
        $dto->display = (bool)($validated['display'] ?? false);
        $dto->age_restrictions = [
            'from' => isset($validated['age_restrictions_from']) ? (int)$validated['age_restrictions_from'] : null,
            'to' => isset($validated['age_restrictions_to']) ? (int)$validated['age_restrictions_to'] : null,
        ];
        $dto->instructor = isset($validated['instructor_id']) ?
            $this->instructorRepository->find($validated['instructor_id'])
            : null;
        $dto->picture = $this->file('picture');
        $dto->instructor_id = $validated['instructor_id'] ?? null;
        $dto->starts_at = isset($validated['starts_at']) ? Carbon::parse($validated['starts_at']) : null;
        $dto->ends_at = isset($validated['ends_at']) ? Carbon::parse($validated['ends_at']) : null;
        $dto->genres = $validated['genres'] ?? [];
        $dto->user = $this->user();

        return $dto;
    }
}
