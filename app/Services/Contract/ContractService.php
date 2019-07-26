<?php
/**
 * File: ContractService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Contract;

use App\Models\Contract;
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
     * @param Contract $contract
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws Exceptions\ContractAlreadySignedException
     */
    public function sign(Contract $contract): void
    {
        if (null !== $contract->terminated_at) {
            throw new Exceptions\ContractAlreadyTerminatedException();
        }

        if (null !== $contract->signed_at) {
            throw new Exceptions\ContractAlreadySignedException();
        }

        $this->contractRepository->sign($contract);
    }

    /**
     * @param Contract $contract
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws Exceptions\ContractAlreadyTerminatedException
     */
    public function terminate(Contract $contract): void
    {
        if (null !== $contract->terminated_at) {
            throw new Exceptions\ContractAlreadyTerminatedException();
        }

        $this->contractRepository->terminate($contract);
    }
}
