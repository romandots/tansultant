<?php

namespace App\Components\Price\Policy\Contract;

interface PricePolicyInterface
{
    public function getPrice(): float;
}