<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $subscription_id
 * @property-read BelongsTo<Subscription>|null $subscription
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $starts_at
 * @property \Carbon\Carbon|null $ends_at
 */
class Hold extends Model
{
    use HasFactory;

    public const TABLE = 'holds';
    protected $table = self::TABLE;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}