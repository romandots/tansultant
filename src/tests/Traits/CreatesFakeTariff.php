<?php

namespace Tests\Traits;

use App\Models\Tariff;

trait CreatesFakeTariff
{
    public function createFakeTariff(array $attributes = []): Tariff
    {
        return Tariff::factory()->create($attributes);
    }
}