<?php

declare(strict_types=1);

namespace App\Components\Payment;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Payment
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
            'credit_id' => $this->credit_id,
            'credit' => $this->whenLoaded('credit', function () {
                return new \App\Components\Credit\Formatter($this->credit);
            }),
            'bonus_id' => $this->bonus_id,
            'bonus' => $this->whenLoaded('bonus', function () {
                return new \App\Components\Bonus\Formatter($this->bonus);
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
