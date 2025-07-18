<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\DTO\SearchFilterDto;
use App\Common\Requests\SearchRequest;
use App\Http\Requests\ManagerApi\DTO\SearchClassroomsFilterDto;

class SearchClassroomsRequest extends SearchRequest
{

    public function __construct()
    {
        parent::__construct();
        $this->searchFilterDtoClass = SearchClassroomsFilterDto::class;
    }

    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'branch_id' => [
                'nullable',
                'string',
                'uuid'
            ],
        ]);
    }

    protected function mapSearchFilterDto(SearchFilterDto $dto, array $datum): void
    {
        assert($dto instanceof SearchClassroomsFilterDto);
        $dto->branch_id = $datum['branch_id'] ?? null;

        parent::mapSearchFilterDto($dto, $datum);
    }
}