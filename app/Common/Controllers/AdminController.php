<?php
declare(strict_types=1);

namespace App\Common\Controllers;

use App\Common\BaseFacade;
use App\Common\Requests\ShowRequest;
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
        return \clock()->event('Serving INDEX action')->run(function () {

            $records = $this->getFacade()->getAll([]);
            return $this->makeResourceCollection($records);

        });
    }

    protected function _search(\App\Common\Requests\SearchRequest $request): AnonymousResourceCollection
    {
        return \clock()->event('Serving SEARCH action')->run(function () use ($request) {

            $searchRecords = $request->getDto();
            $records = $this->getFacade()->search($searchRecords, $this->getSearchRelations());
            $meta = $this->getFacade()->getMeta($searchRecords);

            return $this->makeResourceCollection($records)->additional(['meta' => $meta]);

        });
    }

    public function suggest(SuggestRequest $request): array {
        return $this->_suggest($request);
    }

    final protected function _suggest(
        SuggestRequest $request,
        string|\Closure $labelField = 'name',
        string|\Closure $valueField = 'id',
        array $extraFields = []
    ): array {
        return \clock()->event('Serving SUGGEST action')
            ->run(function () use ($extraFields, $valueField, $labelField, $request) {

                $data = $this->getFacade()->suggest($request->getDto(), $labelField, $valueField, $extraFields);
                return  $this->formatList($data);

            });
    }

    public function show(ShowRequest $request): JsonResource
    {
        return \clock()->event('Serving SHOW action')->run(function () use ($request) {

            $dto = $request->getDto();
            $record = $this->getFacade()->find($dto);
            return $this->makeResource($record);

        });
    }

    protected function _store(StoreRequest $request): JsonResource
    {
        return \clock()->event('Serving STORE action')->run(function () use ($request) {

            $record = $this->getFacade()->create($request->getDto(), $this->getSingleRecordRelations());
            return $this->makeResource($record);

        });
    }

    protected function _update(string $id, StoreRequest $request): JsonResource
    {
        return \clock()->event('Serving UPDATE action')->run(function () use ($id, $request) {

            $record = $this->getFacade()->findAndUpdate($id, $request->getDto(), $this->getSingleRecordRelations());
            return $this->makeResource($record);

        });
    }

    public function destroy(string $id, Request $request): void
    {
        \clock()->event('Serving DESTROY action')->run(function () use ($id, $request) {

            $this->getFacade()->findAndDelete($id, $request->user());

        });
    }

    public function restore(string $id, Request $request): void
    {
        \clock()->event('Serving RESTORE action')->run(function () use ($id, $request) {

            $this->getFacade()->findAndRestore($id, $this->getSingleRecordRelations(), $request->user());

        });
    }

    final protected function getFacade(): BaseFacade
    {
        return $this->facade;
    }

    final protected function makeResource(Model $record): JsonResource
    {
        return \clock()->event('Formatting record')->run(function () use ($record) {
            return new $this->resourceClass($record);
        });
    }

    final protected function makeResourceCollection(Collection $collection): AnonymousResourceCollection
    {
        return \clock()->event('Formatting record')->run(function () use ($collection) {
            return $this->resourceClass::collection($collection);
        });
    }

    final protected function formatList(array $data = []): array
    {
        return ['data' => $data];
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
