<?php
/**
 * File: CustomerController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Common\Controllers\AdminController;
use App\Components\Customer as Component;

/**
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection index()
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection _search(\App\Common\Requests\SearchRequest $request)
 * @method array suggest(\App\Common\Requests\SuggestRequest $request)
 * @method \Illuminate\Http\Resources\Json\JsonResource show(string $id)
 * @method \Illuminate\Http\Resources\Json\JsonResource store(\App\Common\Requests\StoreRequest $request)
 * @method \Illuminate\Http\Resources\Json\JsonResource update(string $id, \App\Common\Requests\UpdateRequest $request)
 * @method void destroy(string $id)
 * @method void restore(string $id)
 * @method Component\Facade getFacade()
 * @method \Illuminate\Http\Resources\Json\JsonResource makeResource(\App\Models\Contract $record)
 * @method \Illuminate\Http\Resources\Json\AnonymousResourceCollection makeResourceCollection(\Illuminate\Support\Collection $collection)
 */
class CustomerController extends AdminController
{
    public function __construct() {
        parent::__construct(
            facadeClass: Component\Facade::class,
            resourceClass: Component\Formatter::class,
            searchRelations: ['classroom', 'instructor.person'],
            singleRecordRelations: ['classroom', 'instructor.person'],
        );
    }
}
