<?php

namespace App\Models;

use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $name
 * @property int $price
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class Price extends Model
{
    use UsesUuid;
    use SoftDeletes;
    //use HasFactory;

    public const TABLE = 'prices';

    public $table = self::TABLE;
}