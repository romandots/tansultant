<?php
/**
 * File: ContractRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;

/**
 * Class ContractRepository
 * @package App\Repository
 */
class ContractRepository
{
    /**
     * @param int $id
     * @return Contract|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(int $id): ?Contract
    {
        return Contract::query()->findOrFail($id);
    }

    /**
     * @param string $customerId
     * @return Contract|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByCustomerId(string $customerId): ?Contract
    {
        return Contract::query()->where('customer_id', $customerId)->firstOrFail();
    }

    /**
     * @param string $customerId
     * @param int|null $branchId
     * @param string|null $serial
     * @return Contract
     * @throws \Exception
     */
    public function create(string $customerId, ?int $branchId = null, ?string $serial = null): Contract
    {
        $contract = new Contract;
        $contract->id = \uuid();
        $contract->serial = $serial ?: $this->getCurrentSerial();
        $contract->number = (int)$this->getLastNumber($contract->serial) + 1;
        $contract->branch_id = $branchId;
        $contract->customer_id = $customerId;
        $contract->status = Contract::STATUS_PENDING;
        $contract->created_at = Carbon::now();
        $contract->save();

        return $contract;
    }

    /**
     * @param Contract $contract
     */
    public function sign(Contract $contract): void
    {
        $contract->status = Contract::STATUS_SIGNED;
        $contract->signed_at = Carbon::now();
        $contract->save();
    }

    /**
     * @param Contract $contract
     */
    public function terminate(Contract $contract): void
    {
        $contract->status = Contract::STATUS_TERMINATED;
        $contract->terminated_at = Carbon::now();
        $contract->save();
    }

    /**
     * @param Contract $contract
     * @throws \Exception
     */
    public function delete(Contract $contract): void
    {
        $contract->delete();
    }

    /**
     * @return string
     */
    private function getCurrentSerial(): string
    {
        $lastRecord = \DB::table(Contract::TABLE)
            ->select('serial')
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->first();

        return $lastRecord ? $lastRecord->serial : '';
    }

    /**
     * @param string $serial
     * @return int|null
     */
    private function getLastNumber(?string $serial = null): ?int
    {
        /** @var Builder $query */
        $query = \DB::table(Contract::TABLE)
            ->select('number');

        if ($serial) {
            $query = $query->where('serial', $serial);
        }

        $lastRecord = $query
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->first();

        return $lastRecord ? (int)$lastRecord->number : null;
    }
}
