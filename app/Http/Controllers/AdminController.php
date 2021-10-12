<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ManagerApi\FilteredPaginatedFormRequest;
use App\Http\Requests\ManagerApi\SuggestRequest;
use App\Http\Resources\ManagerApi\LessonResource;
use App\Services\BaseFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

abstract class AdminController extends Controller
{
    abstract public function getFacade(): BaseFacade;
    abstract public function makeResource(Model $record): JsonResource;
    abstract public function makeResourceCollection(Collection $collection): AnonymousResourceCollection;

    public function index(): AnonymousResourceCollection
    {
        $records = $this->getFacade()->getAll();
        return $this->makeResourceCollection($records);
    }

    public function search(FilteredPaginatedFormRequest $request): AnonymousResourceCollection
    {
        $searchLessons = $request->getDto();
        $lessons = $this->getFacade()->search($searchLessons, $this->getSearchRelations());
        $meta = $this->getFacade()->getMeta($searchLessons);

        return LessonResource::collection($lessons)->additional(['meta' => $meta]);
    }

    protected function getSearchRelations(): array
    {
        return [];
    }

    public function suggest(SuggestRequest $request): array
    {
        return $this->getFacade()->suggest($request->getQuery());
    }

    public function show(string $id): JsonResource
    {
        $record = $this->getFacade()->find($id);
        return $this->makeResource($record);
    }

    public function destroy(string $id): void
    {
        $this->getFacade()->findAndDelete($id);
    }

    public function restore(string $id): void
    {
        $this->getFacade()->restore($id);
    }
}
