<?php

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Common\Controllers\AdminController;
use App\Components\Shift as Component;
use App\Http\Requests\ManagerApi\StoreShiftRequest;
use Illuminate\Http\Request;

class ShiftController extends AdminController
{
    public function __construct() {
        parent::__construct(
            facadeClass: Component\Facade::class,
            resourceClass: Component\Formatter::class,
            searchRelations: [],
            singleRecordRelations: [],
        );
    }

    public function store(StoreShiftRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_store($request);
    }

    public function closeActiveShift(Request $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        if (null === $request->user()) {
            throw new \LogicException('user_is_not_defined');
        }

        return $this->getFacade()->closeActiveShift($request->user());
    }
}
