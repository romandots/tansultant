<?php
/**
 * File: CreateFakeClassroom.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Classroom;

/**
 * Trait CreateFakeClassroom
 * @package Tests\Traits
 */
trait CreateFakeClassroom
{
    /**
     * @param array|null $attributes
     * @return Classroom
     */
    private function createFakeClassroom(?array $attributes = []): Classroom
    {
        return \factory(Classroom::class)->create($attributes);
    }
}
