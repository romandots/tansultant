<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\Contracts\SearchFilterDtoContract;
use App\Common\Requests\SearchRequest;
use App\Http\Requests\ManagerApi\DTO\SearchInstructorsFilterDto;
use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Course;
use Illuminate\Validation\Rule;

class SearchLessonsRequest extends SearchRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->paginationDto = new DTO\SearchLessonsDto();
        $this->filterDto = new DTO\SearchLessonsFilterDto();
        $this->sortable = ['name', 'status', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
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
        ]);
    }

    protected function getSearchFilterDto(): SearchFilterDtoContract
    {
        $filter = parent::getSearchFilterDto();
        if (!$filter instanceof SearchInstructorsFilterDto) {
            return $filter;
        }

        $validated = $this->validated();
        $filter->statuses = isset($validated['statuses']) ? (array)$validated['statuses'] : null;
        $filter->display = isset($validated['display']) ? (bool)$validated['display'] : null;

        return $filter;
    }
}