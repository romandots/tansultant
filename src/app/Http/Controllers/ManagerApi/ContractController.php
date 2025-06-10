<?php
/**
 * File: ContractController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Components\Contract as Component;
use App\Components\Loader;
use App\Http\Requests\ManagerApi\SearchContractsRequest;
use App\Http\Requests\ManagerApi\StoreContractRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
class ContractController extends \App\Common\Controllers\AdminController
{
    protected \App\Components\Customer\Facade $customers;

    public function __construct(
    ) {
        parent::__construct(
            facadeClass: Component\Facade::class,
            resourceClass: Component\Formatter::class,
            searchRelations: ['customer.person', 'student.person'],
            singleRecordRelations: ['customer.person', 'student.person'],
        );
        $this->customers = Loader::customers();
    }

    public function search(SearchContractsRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return $this->_search($request);
    }

    public function store(StoreContractRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_store($request);
    }

    public function update(string $id, StoreContractRequest $request): \Illuminate\Http\Resources\Json\JsonResource
    {
        return $this->_update($id, $request);
    }

    /**
     * @param string $customerId
     * @return void
     * @throws ModelNotFoundException
     */
    public function sign(string $customerId): void
    {
        $customer = $this->customers->find($customerId);
        if (null === $customer->contract) {
            throw new ModelNotFoundException('contract_not_found');
        }
        $this->getFacade()->sign($customer->contract);
    }

    /**
     * @param string $customerId
     * @return void
     * @throws ModelNotFoundException
     */
    public function terminate(string $customerId): void
    {
        $customer = $this->customers->find($customerId);
        if (null === $customer->contract) {
            throw new ModelNotFoundException('contract_not_found');
        }
        $this->getFacade()->terminate($customer->contract);
    }
}
