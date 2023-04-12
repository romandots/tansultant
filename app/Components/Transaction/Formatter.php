<?php

declare(strict_types=1);

namespace App\Components\Transaction;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Transaction
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
            'amount' => $this->amount,
            'type' => $this->type,
            'type_label' => \translate('transaction.type', $this->type),
            'transfer_type' => $this->transfer_type,
            'transfer_type_label' => \translate('transaction.transfer_type', $this->transfer_type),
            'status' => $this->status,
            'status_label' => \translate('transaction.status', $this->status),
            'account' => $this->whenLoaded('account', function () {
                return new \App\Components\Account\Formatter($this->account);
            }),
            'user' => $this->whenLoaded('user', function () {
                return new \App\Components\User\Formatter($this->user);
            }),
            'external_id' => $this->external_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'confirmed_at' => $this->confirmed_at?->toDateTimeString(),
            'canceled_at' => $this->canceled_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
        ];
    }
}
