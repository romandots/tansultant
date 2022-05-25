<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\DTO\SearchFilterDto;
use App\Common\Requests\SearchRequest;
use App\Http\Requests\ManagerApi\DTO\SearchLessonsFilterDto;
use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Course;
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
        ]);
    }

    protected function mapSearchFilterDto(SearchFilterDto $dto, array $datum): void
    {
        assert($dto instanceof SearchLessonsFilterDto);
        $dto->branch_id = $datum['branch_id'] ?? null;
        $dto->classroom_id = $datum['classroom_id'] ?? null;
        $dto->course_id = $datum['course_id'] ?? null;
        $dto->statuses = isset($datum['statuses']) ? (array)$datum['statuses'] : [];

        parent::mapSearchFilterDto($dto, $datum);
    }
}