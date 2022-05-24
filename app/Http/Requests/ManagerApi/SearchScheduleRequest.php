<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\DTO\SearchFilterDto;
use App\Common\Requests\SearchRequest;
use App\Http\Requests\ManagerApi\DTO\SearchSchedulesFilterDto;
use Carbon\Carbon;

class SearchScheduleRequest extends SearchRequest
{

    public function __construct()
    {
        parent::__construct();
        $this->searchFilterDtoClass = SearchSchedulesFilterDto::class;
    }

    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'branch_id' => [
                'nullable',
                'string',
                'uuid'
            ],
            'date' => [
                'nullable',
                'string',
                'date',
            ],
        ]);
    }

    protected function mapSearchFilterDto(SearchFilterDto $dto, array $datum): void
    {
        assert($dto instanceof SearchSchedulesFilterDto);
        $dto->date = isset($datum['date']) ? Carbon::parse($datum['date']) : null;
        $dto->branch_id = $datum['branch_id'] ?? null;

        parent::mapSearchFilterDto($dto, $datum);
    }
}