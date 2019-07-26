<?php
/**
 * File: CreatesFakeContract.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Contract;

/**
 * Trait CreatesFakeContract
 */
trait CreatesFakeContract
{
    /**
     * @param array|null $attributes
     * @return Contract
     */
    private function createFakeContract(?array $attributes = []): Contract
    {
        if (!isset($attributes['customer_id'])) {
            $customer = $this->createFakeCustomer();
            $attributes['customer_id'] = $customer->id;
        }

        return \factory(Contract::class)->create($attributes);
    }
}
