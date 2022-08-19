<?php

namespace App\Http\Requests\ManagerApi;

use App\Common\DTO\SearchDto;
use App\Common\Requests\SearchRequest;
use App\Http\Requests\ManagerApi\DTO\SearchStudentsFilterDto;

class SearchStudentsRequest extends SearchRequest
{

    public function __construct()
    {
        parent::__construct();
        $this->searchDtoClass = SearchDto::class;
        $this->searchFilterDtoClass = SearchStudentsFilterDto::class;
        $this->sortable = [
            'name',
            'id',
            'created_at',
            'updated_at',
        ];
    }
}