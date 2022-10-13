<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\DTO\SearchFilterDto;
use App\Common\Requests\SearchRequest;
use App\Http\Requests\ManagerApi\DTO\SearchPaymentsFilterDto;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class SearchPaymentsRequest extends SearchRequest
{

    public function __construct()
    {
        parent::__construct();
        $this->searchFilterDtoClass = SearchPaymentsFilterDto::class;
        $this->sortable = ['name', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'date' => [
                'nullable',
                'date',
            ],
            'customer_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists(Customer::TABLE, 'id')
            ],
        ]);
    }

    protected function mapSearchFilterDto(SearchFilterDto $dto, array $datum): void
    {
        assert($dto instanceof SearchPaymentsFilterDto);
        $dto->customer_id = $datum['customer_id'] ?? null;
        $dto->date = isset($datum['date']) ? Carbon::parse($datum['date']) : null;

        parent::mapSearchFilterDto($dto, $datum);
    }
}