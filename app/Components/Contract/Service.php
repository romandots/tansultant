<?php

declare(strict_types=1);

namespace App\Components\Contract;

use App\Common\BaseService;
use App\Models\Contract;

/**
 * @method Repository getRepository()
 */
class Service extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            Contract::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * @param Contract $contract
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws Exceptions\ContractAlreadySignedException|\Throwable
     */
    public function sign(Contract $contract): void
    {
        if (null !== $contract->terminated_at) {
            $this->error('Failed signing contract #' . $contract->id . ': already terminated', [
                'contract' => $contract->toArray(),
            ]);
            throw new Exceptions\ContractAlreadyTerminatedException();
        }

        if (null !== $contract->signed_at) {
            $this->error('Failed signing contract #' . $contract->id . ': already signed', [
                'contract' => $contract->toArray(),
            ]);
            throw new Exceptions\ContractAlreadySignedException();
        }

        try {
            $this->debug('Signing contract #' . $contract->id);
            $this->getRepository()->sign($contract);
            $this->debug('Contract #' . $contract->id . ' is signed');
        } catch (\Throwable $exception) {
            $this->error('Failed signing contract #' . $contract->id, [
                'contract' => $contract->toArray(),
                'trace' => $exception->getTrace(),
            ]);
            throw $exception;
        }
    }

    /**
     * @param Contract $contract
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws Exceptions\ContractAlreadyTerminatedException
     */
    public function terminate(Contract $contract): void
    {
        if (null !== $contract->terminated_at) {
            $this->error('Failed terminating contract #' . $contract->id . ': already terminated', [
                'contract' => $contract->toArray(),
            ]);
            throw new Exceptions\ContractAlreadyTerminatedException();
        }

        try {
            $this->debug('Terminating contract #' . $contract->id);
            $this->getRepository()->terminate($contract);
            $this->debug('Contract #' . $contract->id . ' is terminated');
        } catch (\Throwable $exception) {
            $this->error('Failed terminating contract #' . $contract->id, [
                'contract' => $contract->toArray(),
                'trace' => $exception->getTrace(),
            ]);
            throw $exception;
        }
    }
}