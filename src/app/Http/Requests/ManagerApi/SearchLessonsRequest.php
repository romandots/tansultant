<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\DTO\SearchFilterDto;
use App\Common\Requests\SearchRequest;
use App\Http\Requests\ManagerApi\DTO\SearchLessonsFilterDto;
use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Instructor;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class SearchLessonsRequest extends SearchRequest
{

    public function __construct()
    {
        parent::__construct();
        $this->searchFilterDtoClass = SearchLessonsFilterDto::class;
        $this->sortable = ['name', 'status', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'date' => [
                'nullable',
                'date',
            ],
            'starts_from' => [
                'nullable',
                'date',
            ],
            'starts_to' => [
                'nullable',
                'date',
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
            'instructor_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Instructor::TABLE, 'id')
            ],
            'course_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Course::TABLE, 'id')
            ],
        ]);
    }

    protected function mapSearchFilterDto(SearchFilterDto $dto, array $datum): void
    {
        assert($dto instanceof SearchLessonsFilterDto);
        $dto->branch_id = $datum['branch_id'] ?? null;
        $dto->classroom_id = $datum['classroom_id'] ?? null;
        $dto->course_id = $datum['course_id'] ?? null;
        $dto->instructor_id = $datum['instructor_id'] ?? null;
        $dto->statuses = isset($datum['statuses']) ? (array)$datum['statuses'] : [];
        $dto->date = isset($datum['date']) ? Carbon::parse($datum['date']) : null;
        $dto->starts_from = isset($datum['starts_from']) ? Carbon::parse($datum['starts_from']) : null;
        $dto->starts_to = isset($datum['starts_to']) ? Carbon::parse($datum['starts_to']) : null;

        parent::mapSearchFilterDto($dto, $datum);
    }
}