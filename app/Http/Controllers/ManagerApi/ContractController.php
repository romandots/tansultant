<?php
/**
 * File: ContractController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContractResource;
use App\Repository\ContractRepository;
use App\Repository\CustomerRepository;
use App\Services\Contract\ContractService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ContractController
 * @package App\Http\Controllers\Api
 */
class ContractController extends Controller
{
    /**
     * @var ContractRepository
     */
    private $contractRepository;

    /**
     * @var ContractService
     */
    private $contractService;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * ContractController constructor.
     * @param ContractService $contractService
     * @param ContractRepository $contractRepository
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        ContractService $contractService,
        ContractRepository $contractRepository,
        CustomerRepository $customerRepository
    ) {
        $this->contractService = $contractService;
        $this->contractRepository = $contractRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param string $customerId
     * @return ContractResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(string $customerId): ContractResource
    {
        $contract = $this->contractRepository->findByCustomerId($customerId);
        $contract->load('customer');

        return new ContractResource($contract);
    }

    /**
     * @param string $customerId
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \App\Services\Contract\Exceptions\ContractAlreadySignedException
     */
    public function sign(string $customerId): void
    {
        $customer = $this->customerRepository->find($customerId);
        if (null === $customer->contract) {
            throw new NotFoundHttpException('contract_not_found');
        }
        $this->contractService->sign($customer->contract);
    }

    /**
     * @param string $customerId
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \App\Services\Contract\Exceptions\ContractAlreadyTerminatedException
     */
    public function terminate(string $customerId): void
    {
        $customer = $this->customerRepository->find($customerId);
        if (null === $customer->contract) {
            throw new NotFoundHttpException('contract_not_found');
        }
        $this->contractService->terminate($customer->contract);
    }
}
