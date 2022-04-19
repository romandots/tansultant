<?php

declare(strict_types=1);

namespace App\Components\Bonus;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Bonus
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
            'type_label' => \trans('bonus.type.' . $this->type),
            'status' => $this->status,
            'status_label' => \trans('bonus.status.' . $this->status),
//            'promocode' => $this->whenLoaded('promocode', function () {
//                return new  \App\Components\Promocode\Formatter($this->promocode);
//            }),
            'account' => $this->whenLoaded('account', function () {
                return new \App\Components\Account\Formatter($this->account);
            }),
            'user' => $this->whenLoaded('user', function () {
                return new \App\Components\User\Formatter($this->user);
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'activated_at' => $this->activated_at?->toDateTimeString(),
            'expired_at' => $this->expired_at?->toDateTimeString(),
            'canceled_at' => $this->canceled_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
        ];
    }
}
