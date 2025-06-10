<?php
/**
 * File: CreatesFakeBranch.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Branch;

/**
 * Trait CreatesFakeBranch
 * @package Tests\Traits
 */
trait CreatesFakeBranch
{
    /**
     * @param array|null $attributes
     * @return Branch
     */
    protected function createFakeBranch(?array $attributes = []): Branch
    {
        return Branch::factory()->create($attributes);
    }
}
