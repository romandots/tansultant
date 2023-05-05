<?php

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Common\Controllers\AdminController;
use App\Components\Loader;
use App\Components\User as Component;
use App\Http\Requests\ManagerApi\SearchUsersRequest;
use App\Http\Requests\ManagerApi\StoreUserRequest;
use App\Http\Requests\ManagerApi\UpdateUserRequest;
use Illuminate\Http\Request;

/**
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection index()
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection _search(\App\Common\Requests\SearchRequest $request)
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
class UserController extends AdminController
{
    public function __construct() {
        parent::__construct(
            facadeClass: Component\Facade::class,
            resourceClass: Component\Formatter::class,
            searchRelations: [],
            singleRecordRelations: [],
        );
    }

    public function search(SearchUsersRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return $this->_search($request);
    }

    public function store(StoreUserRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return \clock()->event('Serving STORE action')->run(function () use ($request) {
            $record = $this->getFacade()->createFromPerson($request->getDto());
            return $this->makeResource($record);
        });
    }

    public function update(string $id, UpdateUserRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return \clock()->event('Serving UPDATE action')->run(function () use ($id, $request) {

            $record = $this->getFacade()->findById($id);
            $this->getFacade()->updateFromPerson($record, $request->getDto());
            $record->load($this->getSingleRecordRelations());
            return $this->makeResource($record);
        });
    }

    public function reset(string $id, Request $request): void
    {
        $users = Loader::users();
        $author = $request->user();
        $user = $users->findById($id);
        $users->resetPassword($user, $author);
    }
}
