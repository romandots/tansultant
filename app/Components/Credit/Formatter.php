<?php

declare(strict_types=1);

namespace App\Components\Credit;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Credit
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
            'customer' => $this->whenLoaded(
                'customer',
                fn () => new \App\Components\Customer\Formatter($this->customer),
            ),
            'transaction' => $this->whenLoaded(
                'transaction',
                fn () => new \App\Components\Transaction\Formatter($this->transaction),
            ),
            'created_at' => $this->created_at,
        ];
    }
}
