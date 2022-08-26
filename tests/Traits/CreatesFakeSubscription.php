<?php

namespace Tests\Traits;

use App\Models\Subscription;

trait CreatesFakeSubscription
{
    public function createFakeSubscription(array $attributes = []): Subscription
    {
        return Subscription::factory()->create($attributes);
    }
}