<?php
/**
 * File: CreatesFakeCustomer.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

/**
 * Trait CreatesFakeCustomer
 * @package Tests\Traits
 */
trait CreatesFakeCustomer
{
    /**
     * @param array|null $attributes
     * @return \App\Models\Customer
     */
    protected function createFakeCustomer(array $attributes = []): \App\Models\Customer
    {
        if (!isset($attributes['person_id'])) {
            $person = $this->createFakePerson();
            $attributes['person_id'] = $person->id;
        }

        return \App\Models\Customer::factory()->create($attributes);
    }
}
