<?php
/**
 * File: CreatesFakeStudent.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */
declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Student;

/**
 * Trait CreatesFakePerson
 * @package Tests\Traits
 */
trait CreatesFakeStudent
{
    /**
     * @param array|null $attributes
     * @return Student
     */
    protected function createFakeStudent(array $attributes = []): Student
    {
        if (!isset($attributes['person_id'])) {
            $person = $this->createFakePerson();
            $attributes['person_id'] = $person->id;
        }
        $attributes['customer_id'] = null;

        return Student::factory()->create($attributes);
    }
}
