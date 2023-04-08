<?php
/**
 * File: Course.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\CourseStatus;
use App\Models\Enum\SubscriptionStatus;
use App\Models\Enum\TariffStatus;
use App\Models\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Tags\HasTags;

/**
 * Class Course

 * @property string $id
 * @property string $name
 * @property string|null $summary
 * @property string|null $description
 * @property boolean $display
 * @property int[] $age_restrictions ['from' => (int|null), 'to' => (int|null)]
 * @property string|null $picture
 * @property string|null $picture_thumb
 * @property int|null $subscriptions_count
 * @property int|null $active_subscriptions_count
 * @property CourseStatus $status [pending|active|disabled]
 * @property bool $is_working
 * @property string|null $instructor_id
 * @property string|null $formula_id
 * @property \Carbon\Carbon|null $starts_at
 * @property \Carbon\Carbon|null $ends_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|Instructor|null $instructor
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsToMany|Subscription[]|null $subscriptions
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsToMany|Subscription[]|null $active_subscriptions
 * @property-read \Illuminate\Database\Eloquent\Relations\HasMany|Collection<Schedule>|null $schedules
 * @property-read  BelongsToMany|Collection|Tariff[] $tariffs
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|Price|null $price
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|Formula|null $formula
 * @package App\Models
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Course onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereAgeRestrictions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereInstructorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course wherePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course wherePictureThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Course whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Course withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Course withoutTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Course withAnyTags($tags, string $type = null)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Course withAllTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Course withAnyTagsOfAnyType($tags)
 * @mixin \Eloquent
 */
class Course extends Model
{
    use SoftDeletes;
    use UsesUuid;
    use HasTags;
    use HasFactory;

    public const TABLE = 'courses';

    protected $table = self::TABLE;

    protected $casts = [
        'status' => CourseStatus::class,
        'starts_at' => 'date',
        'ends_at' => 'date',
        'age_restrictions' => 'array',
    ];

    public $timestamps = [
        'deleted_at',
    ];

    /**
     * @return BelongsTo<Price>
     */
    public function price(): BelongsTo
    {
        return $this->belongsTo(Price::class);
    }

    public function schedules(): HasMany
    {
        $date = Carbon::now()->toDateString();
        return $this->hasMany(Schedule::class)
            ->whereNull('deleted_at')
            ->where(function (Builder $query) use ($date) {
                $query
                    ->where('from_date', '<=', $date)
                    ->orWhereNull('from_date');
            })
            ->where(function (Builder $query) use ($date) {
                $query
                    ->where('to_date', '>', $date)
                    ->orWhereNull('to_date');
            });
    }

    /**
     * @return BelongsToMany|Collection|Tariff[]
     */
    public function tariffs(): BelongsToMany
    {
        return $this->belongsToMany(Tariff::class, 'tariff_has_courses')
            ->where('status', TariffStatus::ACTIVE);
    }

    /**
     * Check if course is active
     * according to starts_at and ends_at dates
     *
     * @return bool
     */
    public function isInPeriod(): bool
    {
        $now = Carbon::now()->toDateString();
        return (null === $this->starts_at || $this->starts_at->lessThanOrEqualTo($now))
            && (null === $this->ends_at || $this->ends_at->greaterThanOrEqualTo($now));
    }

    /**
     * Check if course is active
     * according to starts_at and ends_at dates
     * and status
     *
     * @return bool
     */
    public function isWorking(): bool
    {
        return CourseStatus::ACTIVE === $this->status && $this->isInPeriod();
    }

    public function getIsWorkingAttribute(): bool
    {
        return $this->isWorking();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Instructor|null
     */
    public function instructor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Instructor::class)->with('person');
    }

    public function formula(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Formula::class);
    }

    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(Subscription::class, Subscription::COURSES_PIVOT_TABLE);
    }


    public function active_subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(Subscription::class, Subscription::COURSES_PIVOT_TABLE)
            ->where('status', SubscriptionStatus::ACTIVE);
    }


}
