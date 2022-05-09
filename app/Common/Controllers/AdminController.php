<?php
declare(strict_types=1);

namespace App\Common\Controllers;

use App\Common\BaseFacade;
use App\Common\Requests\StoreRequest;
use App\Common\Requests\SuggestRequest;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

abstract class AdminController extends Controller
{
    protected BaseFacade $facade;

    public function __construct(
        string $facadeClass,
        protected string $resourceClass,
        protected array $searchRelations = [],
        protected array $singleRecordRelations = [],
    ) {
        $this->facade = \app($facadeClass);
    }

    public function index(): AnonymousResourceCollection
    {
        $records = $this->getFacade()->getAll([]);
        return $this->makeResourceCollection($records);
    }

    protected function _search(\App\Common\Requests\SearchRequest $request): AnonymousResourceCollection
    {
        $searchRecords = $request->getDto();
        $records = $this->getFacade()->search($searchRecords, $this->getSearchRelations());
        $meta = $this->getFacade()->getMeta($searchRecords);

        return $this->makeResourceCollection($records)->additional(['meta' => $meta]);
    }

    public function suggest(SuggestRequest $request): array
    {
        return ['data' => $this->getFacade()->suggest($request->getQuery())];
    }

    public function show(string $id): JsonResource
    {
        $record = $this->getFacade()->find($id, $this->getSingleRecordRelations());
        return $this->makeResource($record);
    }

    protected function _store(StoreRequest $request): JsonResource
    {
        $record = $this->getFacade()->create($request->getDto(), $this->getSingleRecordRelations());
        return $this->makeResource($record);
    }

    protected function _update(string $id, StoreRequest $request): JsonResource
    {
        $record = $this->getFacade()->findAndUpdate($id, $request->getDto(), $this->getSingleRecordRelations());
        return $this->makeResource($record);
    }

    public function destroy(string $id, Request $request): void
    {
        $this->getFacade()->findAndDelete($id, $request->user());
    }

    public function restore(string $id, Request $request): void
    {
        $this->getFacade()->findAndRestore($id, $this->getSingleRecordRelations(), $request->user());
    }

    final protected function getFacade(): BaseFacade
    {
        return $this->facade;
    }

    final protected function makeResource(Model $record): JsonResource
    {
        return new $this->resourceClass($record);
    }

    final protected function makeResourceCollection(Collection $collection): AnonymousResourceCollection
    {
        return $this->resourceClass::collection($collection);
    }

    final protected function getSearchRelations(): array
    {
        return $this->searchRelations;
    }

    final protected function getSingleRecordRelations(): array
    {
        return $this->singleRecordRelations;
    }

}
