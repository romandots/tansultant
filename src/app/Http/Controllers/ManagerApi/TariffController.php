<?php

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Common\Controllers\AdminController;
use App\Common\Requests\ManageRelationsRequest;
use App\Components\Tariff as Component;
use App\Http\Requests\ManagerApi\SearchTariffRequest;
use App\Http\Requests\ManagerApi\StoreTariffRequest;

/**
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection index()
 * @method array suggest(\App\Common\Requests\SuggestRequest $request)
 * @method Component\Formatter show(string $id)
 * @method Component\Formatter _store(\App\Common\Requests\StoreRequest $request)
 * @method Component\Formatter _update(string $id, \App\Common\Requests\StoreRequest $request)
 * @method void destroy(string $id, \Illuminate\Http\Request $request)
 * @method void restore(string $id, \Illuminate\Http\Request $request)
 * @method Component\Facade getFacade()
 * @method \Illuminate\Http\Resources\Json\JsonResource makeResource(\App\Models\Contract $record)
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection makeResourceCollection(\Illuminate\Support\Collection $collection)
 */
class TariffController extends AdminController
{
    public function __construct() {
        parent::__construct(
            facadeClass: Component\Facade::class,
            resourceClass: Component\Formatter::class,
            searchRelations: [],
            singleRecordRelations: [],
        );
    }

    public function search(SearchTariffRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return $this->_search($request);
    }

    public function store(StoreTariffRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_store($request);
    }

    public function update(string $id, StoreTariffRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_update($id, $request);
    }

    public function attachCourses(ManageRelationsRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        $tariff = $this->getFacade()->findAndAttachCourses($request->getDto());
        return new \App\Components\Tariff\Formatter($tariff);
    }

    public function detachCourses(ManageRelationsRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        $tariff = $this->getFacade()->findAndDetachCourses($request->getDto());
        return new \App\Components\Tariff\Formatter($tariff);
    }
}
