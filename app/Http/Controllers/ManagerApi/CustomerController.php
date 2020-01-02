<?php
/**
 * File: CustomerController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManagerApi\AttachCustomerRequest;
use App\Http\Requests\ManagerApi\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Repository\ContractRepository;
use App\Repository\CustomerRepository;
use App\Repository\PersonRepository;
use Illuminate\Support\Facades\DB;


class CustomerController extends Controller
{
    private PersonRepository $personRepository;
    private CustomerRepository $customerRepository;
    private ContractRepository $contractRepository;

    public function __construct(
        CustomerRepository $customerRepository,
        PersonRepository $personRepository,
        ContractRepository $contractRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->personRepository = $personRepository;
        $this->contractRepository = $contractRepository;
    }

    /**
     * @param StoreCustomerRequest $request
     * @return CustomerResource
     */
    public function store(StoreCustomerRequest $request): CustomerResource
    {
        /** @var Customer $customer */
        $customer = DB::transaction(function () use ($request) {
            $person = $this->personRepository->create($request->getPersonDto());
            $customer = $this->customerRepository->create($person);
            $this->contractRepository->create($customer->id);

            return $customer;
        });
        $customer->load('person', 'contract');

        return new CustomerResource($customer);
    }

    /**
     * @param AttachCustomerRequest $request
     * @return CustomerResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function createFromPerson(AttachCustomerRequest $request): CustomerResource
    {
        $dto = $request->getDto();
        $person = $this->personRepository->find($dto->person_id);
        $customer = DB::transaction(function () use ($person) {
            $customer = $this->customerRepository->create($person);
            $this->contractRepository->create($customer->id);

            return $customer;
        });
        $customer->load('person', 'contract');

        return new CustomerResource($customer);
    }

    /**
     * @param string $id
     * @return CustomerResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(string $id): CustomerResource
    {
        $customer = $this->customerRepository->find($id);
        $customer->load('person');

        return new CustomerResource($customer);
    }

    /**
     * @param string $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function destroy(string $id): void
    {
        $customer = $this->customerRepository->find($id);
        $this->customerRepository->delete($customer);
    }
}
