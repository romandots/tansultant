<?php
/**
 * File: CreateFakeClassroom.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Branch;
use App\Models\Classroom;

/**
 * Trait CreateFakeClassroom
 * @package Tests\Traits
 */
trait CreatesFakeClassroom
{
    /**
     * @param array|null $attributes
     * @param Branch|null $branch
     * @return Classroom
     */
    protected function createFakeClassroom(?array $attributes = [], ?Branch $branch = null): Classroom
    {
        $branch = $branch ?? $this->createFakeBranch();
        $attributes['branch_id'] = $attributes['branch_id'] ?? $branch->id;
        return Classroom::factory()->create($attributes);
    }
}
