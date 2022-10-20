<?php

namespace App\Models;

use App\Models\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property string $id
 * @property string $name
 * @property string $tariff_id
 * @property string $student_id
 * @property string|null $hold_id
 * @property int|null $courses_limit
 * @property int|null $visits_limit
 * @property int|null $days_limit
 * @property int|null $holds_limit
 * @property int|null $courses_left
 * @property int|null $visits_left
 * @property int|null $days_left
 * @property int|null $holds_left
 * @property int|null $courses_count
 * @property int|null $visits_count
 * @property int|null $days_count
 * @property int|null $holds_count
 * @property int|null $payments_count
 * @property-read BelongsTo<Tariff>|Tariff|null $tariff
 * @property-read BelongsTo<Student>|Student|null $student
 * @property-read BelongsToMany|Collection<Course>|null $courses
 * @property-read BelongsToMany|Collection<Payment>|null $payments
 * @property-read HasMany<Hold>|Hold|null $holds
 * @property-read BelongsTo<Hold>|Hold|null $active_hold
 * @property-read HasMany<Visit>|Visit|null $visits
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

    public function active_hold(): BelongsTo
    {
        return $this->belongsTo(Hold::class, 'hold_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function getDaysCountAttribute(): ?int
    {
        return $this->activated_at?->diffInDays();
    }

    public function getCoursesLeftAttribute(): ?int
    {
        if (null === $this->courses_limit) {
            return null;
        }

        if (null === $this->courses_count) {
            return $this->courses_limit;
        }

        return $this->courses_limit - $this->courses_count;
    }

    public function getVisitsLeftAttribute(): ?int
    {
        if (null === $this->visits_limit) {
            return null;
        }

        if (null === $this->visits_count) {
            return $this->visits_limit;
        }

        return $this->visits_limit - $this->visits_count;
    }

    public function getHoldsLeftAttribute(): ?int
    {
        if (null === $this->holds_limit) {
            return null;
        }

        if (null === $this->holds_count) {
            return $this->holds_limit;
        }

        return $this->holds_limit - $this->holds_count;
    }

    public function getDaysLeftAttribute(): ?int
    {
        if (null === $this->days_limit) {
            return null;
        }

        if (null === $this->days_count) {
            return $this->days_limit;
        }

        return $this->days_limit - $this->days_count;
    }

    public function canBeProlongated(): bool
    {
        if (null === $this->expired_at) {
            return false;
        }

        $prolongationPeriod = \config('subscriptions.prolongation_extra_period', 0);
        return $this->expired_at->clone()->addDays($prolongationPeriod)->greaterThanOrEqualTo(Carbon::now());
    }
}