<?php

declare(strict_types=1);

namespace App\Components\Account;

use App\Common\BaseFormatter;
use App\Models\Enum\TransactionTransferType;

/**
 * @mixin \App\Models\Account
 */
class Formatter extends BaseFormatter
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'branch_id' => $this->branch_id,
            'external_id' => $this->external_id,
            'external_system' => $this->external_system,
            'branch' => $this->whenLoaded('branch', function () {
                return new \App\Components\Branch\Formatter($this->branch);
            }),
            'is_default_for' => array_map(
                static fn (TransactionTransferType $transactionTransferType) => $transactionTransferType->value,
                $this->is_default_for
            ),
            'is_default_for_labels' => array_map(
                static fn (TransactionTransferType $transactionTransferType) => trans('transaction.transfer_type.' . $transactionTransferType->value),
                $this->is_default_for
            ),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
