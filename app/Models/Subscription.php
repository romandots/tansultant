<?php

namespace App\Models;

use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $name
 * @property string $tariff_id
 * @property string $student_id
 * @property string|null $payment_id
 * @property int|null $courses_limit
 * @property int|null $visits_limit
 * @property int|null $days_limit
 * @property int|null $holds_limit
 * @property int|null $courses_count
 * @property int|null $visits_count
 * @property int|null $days_count
 * @property int|null $holds_count
 * @property-read BelongsTo<Payment>|Payment|null $payment
 * @property-read BelongsTo<Tariff>|Tariff|null $tariff
 * @property-read BelongsTo<Student>|Student|null $student
 * @property-read BelongsToMany<Course>|null $courses
 * @property-read BelongsToMany<Payment>|null $payments
 * @property-read HasMany<Hold>|null $holds
 * @property-read HasMany<Visit>|null $visits
 * @property Enum\SubscriptionStatus $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $activated_at
 * @property \Carbon\Carbon|null $expired_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Subscription extends Model
{
    use HasFactory;
    use UsesUuid;

    public const TABLE = 'subscriptions';
    protected $table = self::TABLE;

    protected $guarded = [];

    protected $casts = [
        'status' => Enum\SubscriptionStatus::class,
        'created_at' => 'datetime',
        'activated_at' => 'datetime',
        'expired_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'subscription_has_courses');
    }

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(Payment::class, 'subscription_has_payments');
    }

    public function holds(): HasMany
    {
        return $this->hasMany(Hold::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Hold::class);
    }

    public function getDaysCountAttribute(): ?int
    {
        return $this->activated_at?->diffInDays();
    }
}