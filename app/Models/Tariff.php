<?php

namespace App\Models;

use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $name
 * @property float $price
 * @property float $prolongation_price
 * @property int|null $courses_count
 * @property int|null $visits_count
 * @property int|null $days_count
 * @property int|null $holds_count
 * @property-read HasMany<Subscription>|null $subscriptions
 * @property-read BelongsToMany<Course>|null $courses
 * @property Enum\TariffStatus $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $archived_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Tariff extends Model
{
    use HasFactory;
    use UsesUuid;

    public const TABLE = 'tariffs';
    protected $table = self::TABLE;

    protected $guarded = [];

    protected $casts = [
        'status' => Enum\TariffStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany('subscriptions')->orderBy('created_at', 'desc');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'tariff_has_courses');
    }
}