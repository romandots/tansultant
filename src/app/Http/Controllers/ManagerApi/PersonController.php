<?php

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Common\Controllers\AdminController;
use App\Components\Person as Component;
use App\Http\Requests\ManagerApi\SearchPeopleRequest;
use App\Http\Requests\ManagerApi\StorePersonRequest;
use App\Http\Requests\ManagerApi\UpdatePersonRequest;

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
class PersonController extends AdminController
{
    public function __construct() {
        parent::__construct(
            facadeClass: Component\Facade::class,
            resourceClass: Component\Formatter::class,
            searchRelations: [],
            singleRecordRelations: ['instructors', 'students', 'customers'],
        );
    }

    public function search(SearchPeopleRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return $this->_search($request);
    }

    public function store(StorePersonRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_store($request);
    }

    public function update(string $id, UpdatePersonRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_update($id, $request);
    }
}
