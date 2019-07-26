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
use App\Services\Contract\ContractService;

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
     * ContractController constructor.
     * @param ContractService $contractService
     * @param ContractRepository $contractRepository
     */
    public function __construct(ContractService $contractService, ContractRepository $contractRepository)
    {
        $this->contractService = $contractService;
        $this->contractRepository = $contractRepository;
    }

    /**
     * @param int $id
     * @return ContractResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(int $id): ContractResource
    {
        $contract = $this->contractRepository->find($id);
        $contract->load('customer');

        return new ContractResource($contract);
    }


    /**
     * @param int $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \App\Services\Contract\Exceptions\ContractAlreadySignedException
     */
    public function sign(int $id): void
    {
        $this->contractService->sign($id);
    }

    /**
     * @param int $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \App\Services\Contract\Exceptions\ContractAlreadyTerminatedException
     */
    public function terminate(int $id): void
    {
        $this->contractService->terminate($id);
    }
}
