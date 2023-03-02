<?php
/**
 * File: Lesson.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\LessonStatus;
use App\Models\Enum\LessonType;
use App\Models\Enum\VisitEventType;
use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Lesson
 *
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property string|null $course_id
 * @property string|null $schedule_id
 * @property string|null $instructor_id
 * @property string|null $controller_id
 * @property string|null $price_id
 * @property string|null $payment_id
 * @property string $branch_id
 * @property string $classroom_id
 * @property LessonType $type [lesson | event | rent]
 * @property LessonStatus $status [booked | ongoing | passed | canceled | closed]
 * @property \Carbon\Carbon $starts_at
 * @property \Carbon\Carbon $ends_at
 * @property \Carbon\Carbon|null $closed_at
 * @property \Carbon\Carbon|null $canceled_at
 * @property \Carbon\Carbon|null $checked_out_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Models\User $controller
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\Instructor $instructor
 * @property-read \App\Models\Classroom $classroom
 * @property-read \App\Models\Schedule|null $schedule
 * @property-read \App\Models\Branch|null $branch
 * @property-read \App\Models\Price|null $price
 * @property-read \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Collection<Intent> $intents
 * @property-read \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Collection<Visit> $visits
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsToMany|\Illuminate\Database\Eloquent\Collection<Payout> $payouts
 * @property-read int|null $visits_limit
 * @property-read int|null $visits_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Lesson onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson query()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Lesson withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Lesson withoutTrashed()
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereCanceledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereClassroomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereClosedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereControllerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereInstructorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereScheduleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Lesson wherePaymentId($value)
 * @mixin \Eloquent
 */
class Lesson extends Model
{
    use SoftDeletes;
    use UsesUuid;
    use HasFactory;

    public const TABLE = 'lessons';

    protected $table = self::TABLE;

    protected $casts = [
        'status' => LessonStatus::class,
        'type' => LessonType::class,
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'closed_at' => 'datetime',
        'canceled_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Instructor|null
     */
    public function instructor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Instructor::class)->with('person');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Schedule|null
     */
    public function schedule(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Classroom|null
     */
    public function classroom(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Course|null
     */
    public function course(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|User|null
     */
    public function controller(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->with('person');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Visit[]
     */
    public function visits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this
            ->hasMany(Visit::class, 'event_id')
            ->where('event_type', VisitEventType::fromClass(self::class));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Intent[]
     */
    public function intents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this
            ->hasMany(Intent::class, 'event_id')
            ->where('event_type', VisitEventType::fromClass(self::class));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Branch|null
     */
    public function branch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Price>|null
     */
    public function price(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Price::class);
    }

    public function payouts(): BelongsToMany
    {
        return $this->belongsToMany(Payout::class, 'lesson_payout');
    }
}
