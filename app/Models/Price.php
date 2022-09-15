<?php

namespace App\Models;

/**
 * @property string $id
 * @property string $name
 * @property float $price
 */
class Price extends Model
{
    public const TABLE = 'prices';

    public $table = self::TABLE;
}