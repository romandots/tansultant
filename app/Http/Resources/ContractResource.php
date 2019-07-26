<?php
/**
 * File: ContractResource.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ContractResource
 * @package App\Http\Resources
 * @mixin \App\Models\Contract
 */
class ContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request $request
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
                return new CustomerResource($this->customer);
            }),
            'status' => $this->status,
            'status_label' => \trans($this->status),
            'is_signed' => (bool)$this->signed_at,
            'is_terminated' => (bool)$this->terminated_at,
            'is_pending' => null === $this->terminated_at && null === $this->signed_at,
            'created_at' => $this->created_at->toDateTimeString(),
            'signed_at' => $this->signed_at ? $this->signed_at->toDateTimeString() : null,
            'terminated_at' => $this->terminated_at ? $this->terminated_at->toDateTimeString() : null,
        ];
    }
}
