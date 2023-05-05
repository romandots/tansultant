<?php

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Common\Controllers\AdminController;
use App\Common\Requests\ShowRequest;
use App\Components\Loader;
use App\Components\Shift as Component;
use App\Http\Requests\ManagerApi\StoreShiftRequest;
use App\Services\Permissions\ShiftsPermission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ShiftController extends AdminController
{
    public function __construct()
    {
        parent::__construct(
            facadeClass: Component\Facade::class,
            resourceClass: Component\Formatter::class,
            searchRelations: [],
            singleRecordRelations: ['branch'],
        );
    }

    protected function search(\App\Common\Requests\SearchRequest $request): AnonymousResourceCollection
    {
        return $this->_search($request);
    }

    public function show(ShowRequest $request): JsonResource
    {
        $user = $request->user();
        if (!$user->canAny(ShiftsPermission::READ, ShiftsPermission::MANAGE) &&
            !Loader::shifts()->isShiftBelongToUser($request->getDto()->id, $user->id)) {
            throw new Component\Exceptions\ShiftDoesNotBelongToUserException();
        }

        return parent::show($request);
    }


    public function store(StoreShiftRequest $request): JsonResource
    {
        return $this->_store($request);
    }

    public function getActiveShift(Request $request): JsonResource
    {
        return $this->getFacade()->getActiveShift($request->user(), $this->getSingleRecordRelations());
    }

    public function closeActiveShift(Request $request): JsonResource
    {
        if (null === $request->user()) {
            throw new \LogicException('user_is_not_defined');
        }

        return $this->getFacade()->closeActiveShift($request->user(), $this->getSingleRecordRelations());
    }

    public function getTransactions(Request $request, string $id): JsonResource
    {
        return $this->getFacade()->getShiftTransactions($id, $request->user());
    }
}
