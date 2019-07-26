<?php
/**
 * File: ContractService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Contract;

use App\Repository\ContractRepository;

/**
 * Class ContractService
 * @package App\Service\Contract
 */
class ContractService
{
    /**
     * @var ContractRepository
     */
    private $contractRepository;

    /**
     * ContractController constructor.
     * @param ContractRepository $contractRepository
     */
    public function __construct(ContractRepository $contractRepository)
    {
        $this->contractRepository = $contractRepository;
    }

    /**
     * @param int $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws Exceptions\ContractAlreadySignedException
     */
    public function sign(int $id): void
    {
        $contract = $this->contractRepository->find($id);

        if (null !== $contract->terminated_at) {
            throw new Exceptions\ContractAlreadyTerminatedException();
        }

        if (null !== $contract->signed_at) {
            throw new Exceptions\ContractAlreadySignedException();
        }

        $this->contractRepository->sign($contract);
    }

    /**
     * @param int $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws Exceptions\ContractAlreadyTerminatedException
     */
    public function terminate(int $id): void
    {
        $contract = $this->contractRepository->find($id);

        if (null !== $contract->terminated_at) {
            throw new Exceptions\ContractAlreadyTerminatedException();
        }

        $this->contractRepository->terminate($contract);
    }
}
