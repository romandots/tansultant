<?php

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Common\Controllers\AdminController;
use App\Common\Requests\ManageRelationsRequest;
use App\Components\Loader;
use App\Components\Subscription as Component;
use App\Http\Requests\ManagerApi\SearchSubscriptionRequest;
use App\Http\Requests\ManagerApi\StoreSubscriptionRequest;
use App\Http\Requests\ManagerApi\UpdateSubscriptionStatusRequest;

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
class SubscriptionController extends AdminController
{
    public function __construct() {
        parent::__construct(
            facadeClass: Component\Facade::class,
            resourceClass: Component\Formatter::class,
            searchRelations: ['student', 'tariff',],
            singleRecordRelations: ['student', 'tariff', 'courses', 'payments',],
        );
    }

    public function search(SearchSubscriptionRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return $this->_search($request);
    }

    public function store(StoreSubscriptionRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_store($request);
    }

    public function update(string $id, StoreSubscriptionRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_update($id, $request);
    }

    public function attachCourses(ManageRelationsRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        $subscription = $this->getFacade()->findAndAttachCourses($request->getDto());
        return new \App\Components\Subscription\Formatter($subscription);
    }

    public function detachCourses(ManageRelationsRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        $subscription = $this->getFacade()->findAndDetachCourses($request->getDto());
        return new \App\Components\Subscription\Formatter($subscription);
    }

    public function setStatus(UpdateSubscriptionStatusRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        $statusDto = $request->getDto();
        assert($statusDto instanceof Component\StatusDto);
        $subscription = Loader::subscriptions()->updateStatus($statusDto);
        return new \App\Components\Subscription\Formatter($subscription);
    }
}
