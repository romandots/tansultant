<?php

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Common\Controllers\AdminController;
use App\Components\Payout as Component;
use App\Http\Requests\ManagerApi\DeletePayoutLessonsRequest;
use App\Http\Requests\ManagerApi\DeletePayoutsRequest;
use App\Http\Requests\ManagerApi\SearchPayoutsRequest;
use App\Http\Requests\ManagerApi\StoreBatchPayoutRequest;
use App\Http\Requests\ManagerApi\StorePayoutRequest;
use App\Http\Requests\ManagerApi\TransitionPayoutsRequest;
use App\Http\Requests\ManagerApi\UpdatePayoutLessonsRequest;

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
class PayoutController extends AdminController
{
    public function __construct() {
        parent::__construct(
            facadeClass: Component\Facade::class,
            resourceClass: Component\Formatter::class,
            searchRelations: [],
            singleRecordRelations: [],
        );
    }

    public function search(SearchPayoutsRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return $this->_search($request);
    }

    public function store(StorePayoutRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_store($request);
    }

    public function storeBatch(StoreBatchPayoutRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return $this->makeResourceCollection(
            $this->getFacade()->createBatch($request->getDto())
        );
    }

    public function update(string $id, StorePayoutRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_update($id, $request);
    }

    public function attachLessons(UpdatePayoutLessonsRequest $request): void
    {
        $this->getFacade()->attachLessons($request->getDto());
    }

    public function detachLessons(DeletePayoutLessonsRequest $request): void
    {
        $this->getFacade()->detachLessons($request->getDto());
    }

    public function transitionBatch(TransitionPayoutsRequest $request): void
    {
        $this->getFacade()->transitionBatch($request->getDto());
    }

    public function deleteBatch(DeletePayoutsRequest $request): void
    {
        $this->getFacade()->deleteBatch($request->getDto());
    }
}
