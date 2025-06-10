<?php
/**
 * File: CreatesFakePerson.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */
declare(strict_types=1);

namespace Tests\Traits;

/**
 * Trait CreatesFakePerson
 * @package Tests\Traits
 */
trait CreatesFakePerson
{
    /**
     * @param array|null $attributes
     * @return \App\Models\Person
     */
    protected function createFakePerson(array $attributes = []): \App\Models\Person
    {
        return \App\Models\Person::factory()->create($attributes);
    }
}
