<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property int $amount
 * @property string $credit_id
 * @property string|null $bonus_id
 * @property \Carbon\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo<Credit> $credit
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo<Bonus>|null $bonus
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use UsesUuid;
    //use HasFactory;

    public const TABLE = 'payments';
    public const UPDATED_AT = false;

    protected $table = self::TABLE;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Credit>
     */
    public function credit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Credit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Bonus>
     */
    public function bonus(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Bonus::class);
    }
}
