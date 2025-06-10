<?php

namespace App\Components\Payment\Exceptions;

class BonusIsBiggerThanPrice extends Exception
{
    public function __construct(public readonly \App\Models\Bonus $bonus, public readonly int $price)
    {
        parent::__construct('bonus_is_bigger_than_price', [
            'bonus' => $this->bonus->amount,
            'price' => $this->price,
        ], 409);
    }
}