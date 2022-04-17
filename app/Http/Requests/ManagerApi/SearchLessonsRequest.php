<?php

namespace App\Http\Requests\ManagerApi;

class SearchLessonsRequest extends FilteredPaginatedFormRequest
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

        ]);
    }
}