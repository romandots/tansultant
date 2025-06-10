<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\DTO\SearchDto;
use App\Common\DTO\SearchFilterDto;
use App\Common\Requests\SearchRequest;
use App\Http\Requests\ManagerApi\DTO\SearchVisitsFilterDto;

class SearchVisitsRequest extends SearchRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->searchDtoClass = SearchDto::class;
        $this->searchFilterDtoClass = SearchVisitsFilterDto::class;
        $this->sortable = ['created_at'];
    }

    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'lesson_id' => [
                'nullable',
                'string',
                'uuid',
            ],
            'date' => [
                'nullable',
                'date',
            ],
        ]);
    }

    protected function mapSearchFilterDto(SearchFilterDto $dto, array $datum): void
    {
        assert($dto instanceof SearchVisitsFilterDto);
        $dto->date = isset($datum['date']) ? \Carbon\Carbon::parse($datum['date']) : null;
        $dto->lesson_id = $datum['lesson_id'] ?? null;

        parent::mapSearchFilterDto($dto, $datum);
    }
}