<?php
/**
 * File: ContractController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\Api;

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
     * @param int $customerId
     * @return ContractResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(int $customerId): ContractResource
    {
        $contract = $this->contractRepository->findByCustomerId($customerId);
        $contract->load('customer');

        return new ContractResource($contract);
    }

    /**
     * @param int $customerId
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \App\Services\Contract\Exceptions\ContractAlreadySignedException
     */
    public function sign(int $customerId): void
    {
        $customer = $this->customerRepository->find($customerId);
        if (null === $customer->contract) {
            throw new NotFoundHttpException('contract_not_found');
        }
        $this->contractService->sign($customer->contract);
    }

    /**
     * @param int $customerId
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \App\Services\Contract\Exceptions\ContractAlreadyTerminatedException
     */
    public function terminate(int $customerId): void
    {
        $customer = $this->customerRepository->find($customerId);
        if (null === $customer->contract) {
            throw new NotFoundHttpException('contract_not_found');
        }
        $this->contractService->terminate($customer->contract);
    }
}
