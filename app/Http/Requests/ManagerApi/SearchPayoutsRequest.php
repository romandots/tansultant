<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\DTO\SearchDto;
use App\Common\DTO\SearchFilterDto;
use App\Common\Requests\SearchRequest;
use App\Http\Requests\ManagerApi\DTO\SearchPayoutsFilterDto;
use App\Models\Payout;
use Illuminate\Validation\Rule;

class SearchPayoutsRequest extends SearchRequest
{
    public function __construct()
    {
        parent::__construct();
        $this->searchDtoClass = SearchDto::class;
        $this->searchFilterDtoClass = SearchPayoutsFilterDto::class;
        $this->sortable = ['created_at'];
    }

    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'ids' => [
                'array',
                'nullable',
            ],
            'ids.*' => [
                'string',
                'uuid',
                Rule::exists(Payout::TABLE, 'id'),
                'required_with:ids',
            ],
        ]);
    }

    protected function mapSearchFilterDto(SearchFilterDto $dto, array $datum): void
    {
        assert($dto instanceof SearchPayoutsFilterDto);

        $dto->ids = $datum['ids'] ?? null;

        parent::mapSearchFilterDto($dto, $datum);
    }
}