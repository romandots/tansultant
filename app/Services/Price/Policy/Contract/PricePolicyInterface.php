<?php

namespace App\Services\Price\Policy\Contract;

interface PricePolicyInterface
{
    public function getPrice(): float;
}