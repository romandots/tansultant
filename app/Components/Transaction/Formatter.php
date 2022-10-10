<?php

declare(strict_types=1);

namespace App\Components\Transaction;

use App\Common\BaseFormatter;
use App\Models\Lesson;
use App\Models\Visit;

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
            'transfer_type' => $this->transfer_type,
            'transfer_type_label' => \translate('payment.transfer_type', $this->transfer_type),
            'status' => $this->status,
            'status_label' => \translate('payment.status', $this->status),
            'object_type' => $this->object_type,
            'object' => $this->whenLoaded('object', function () {
                return match ($this->object_type) {
                    Lesson::class => new \App\Components\Lesson\Formatter($this->object),
                    Visit::class => new \App\Components\Visit\Formatter($this->object),
                    default => null,
                };
            }),
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
