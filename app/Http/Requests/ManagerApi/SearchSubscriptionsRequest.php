<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\DTO\SearchDto;
use App\Common\DTO\SearchFilterDto;
use App\Common\Requests\SearchRequest;
use App\Http\Requests\ManagerApi\DTO\SearchSubscriptionsFilterDto;
use App\Models\Course;
use App\Models\Student;
use App\Models\Tariff;
use Illuminate\Validation\Rule;

class SearchSubscriptionsRequest extends SearchRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->searchDtoClass = SearchDto::class;
        $this->searchFilterDtoClass = SearchSubscriptionsFilterDto::class;
        $this->sortable = [
            'name',
            'id',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @param SearchSubscriptionsFilterDto $dto
     * @param array $datum
     * @return void
     */
    protected function mapSearchFilterDto(SearchFilterDto $dto, array $datum): void
    {
        assert($dto instanceof SearchSubscriptionsFilterDto);
        parent::mapSearchFilterDto($dto, $datum);
        $dto->student_id = $datum['student_id'] ?? null;
        $dto->tariff_id = $datum['tariff_id'] ?? null;
        $dto->courses_ids = $datum['courses_ids'] ?? [];
    }

    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'student_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Student::TABLE, 'id'),
            ],
            'tariff_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Tariff::TABLE, 'id'),
            ],
            'courses_ids' => [
                'nullable',
                'array',
            ],
            'courses_ids.*' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Course::TABLE, 'id'),
            ],
        ]);
    }
}