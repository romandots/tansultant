<?php

namespace App\Http\Controllers\ManagerApi;

use App\Components\Loader;
use App\Http\Controllers\Controller;
use App\Http\Requests\ManagerApi\SearchRequest;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    public function index(SearchRequest $request): JsonResponse
    {
        $searchParams = $request->getDto();
        $records = Loader::search()->search($searchParams);
        return new JsonResponse($records);
    }
}