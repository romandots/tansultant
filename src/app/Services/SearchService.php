<?php

namespace App\Services;

use App\Common\BaseService;
use App\Components\Loader;
use App\Http\Requests\ManagerApi\DTO\SearchDto;

class SearchService  extends BaseService
{
    public function search(SearchDto $searchParams): array
    {
        $results = Loader::people()->quickSearch($searchParams->query, $searchParams->limit);
        $this->getLogger()->info('Search query', [
            'query' => $searchParams->query,
            'limit' => $searchParams->limit,
        ]);
        return $results;
    }
}