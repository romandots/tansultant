<?php
/**
 * File: CreatesFakeInstructor.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-19
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Instructor;

/**
 * Class CreatesFakeInstructor
 * @package Tests\Traits
 */
trait CreatesFakeInstructor
{
    /**
     * @param array $attributes
     * @return Instructor
     */
    protected function createFakeInstructor(array $attributes = []): Instructor
    {
        if (!isset($attributes['person_id'])) {
            $person = $this->createFakePerson();
            $attributes['person_id'] = $person->id;
        }

        return Instructor::factory()->create($attributes);
    }
}
