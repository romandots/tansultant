<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ManagerApi\FilteredPaginatedFormRequest;
use App\Http\Requests\ManagerApi\StoreClassroomRequest;
use App\Http\Requests\ManagerApi\StoreClassroomRequest as UpdateClassroomRequest;
use App\Http\Resources\ManagerApi\ClassroomResource;
use App\Services\BaseFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
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

//    public function search(FilteredPaginatedFormRequest $request): AnonymousResourceCollection
//    {
//        $records = $this->getFacade()->search($request, $this->getSearchRelations());
//
//        return ClassroomResource::collection($records);
//    }

    protected function getSearchRelations(): array
    {
        return [];
    }

    protected function _store(FormRequest $request): JsonResource
    {
        $record = $this->getFacade()->create($request->getDto());
        return $this->makeResource($record);
    }

    public function show(string $id): JsonResource
    {
        $record = $this->getFacade()->find($id);
        return $this->makeResource($record);
    }

    protected function _update(FormRequest $request, string $id): JsonResource
    {
        $record = $this->getFacade()->findAndUpdate($id, $request->getDto());
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
