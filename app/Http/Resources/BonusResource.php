<?php
/**
 * File: BonusResource.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class BonusResource
 * @package App\Http\Resources
 * @mixin \App\Models\Bonus
 */
class BonusResource extends JsonResource
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
            'type_label' => \trans('bonus.type.' . $this->type),
            'status' => $this->status,
            'status_label' => \trans('bonus.status.' . $this->status),
//            'promocode' => $this->whenLoaded('promocode', function () {
//                return new PromocodeResource($this->promocode);
//            }),
            'account' => $this->whenLoaded('account', function () {
                return new AccountResource($this->account);
            }),
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'activated_at' => $this->activated_at ? $this->activated_at->toDateTimeString() : null,
            'expired_at' => $this->expired_at ? $this->expired_at->toDateTimeString() : null,
            'canceled_at' => $this->canceled_at ? $this->canceled_at->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
            'deleted_at' => $this->deleted_at ? $this->deleted_at->toDateTimeString() : null,
        ];
    }
}
