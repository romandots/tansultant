<?php
/**
 * File: CreatesFakeLesson.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Lesson;

/**
 * Trait CreatesFakeLesson
 * @package Tests\Traits
 */
trait CreatesFakeLesson
{
    /**
     * @param array|null $attributes
     * @return Lesson
     */
    private function createFakeLesson(?array $attributes = []): Lesson
    {
        return \factory(Lesson::class)->create($attributes);
    }
}
