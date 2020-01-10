<?php
/**
 * File: HasName.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-10
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Models\Traits;

/**
 * Trait HasName
 * @package App\Models\Traits
 * @property string $name
 */
trait HasName
{
    public function __toString(): string
    {
        return $this->name;
    }
}
