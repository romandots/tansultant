<?php

namespace App\Models\Contracts;

interface HasUniqueFields
{
    public function uniqueFields(): array;
}