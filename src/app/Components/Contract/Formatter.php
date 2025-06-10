<?php

declare(strict_types=1);

namespace App\Components\Contract;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Contract
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
            'serial' => $this->serial,
            'number' => $this->number,
            'branch_id' => $this->branch_id,
            'customer' => $this->whenLoaded('customer', function () {
                return new \App\Components\Customer\Formatter($this->customer);
            }),
            'status' => $this->status,
            'status_label' => \trans($this->status),
            'is_signed' => (bool)$this->signed_at,
            'is_terminated' => (bool)$this->terminated_at,
            'is_pending' => null === $this->terminated_at && null === $this->signed_at,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'signed_at' => $this->signed_at?->toDateTimeString(),
            'terminated_at' => $this->terminated_at?->toDateTimeString(),
        ];
    }
}
