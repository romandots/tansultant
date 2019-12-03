<?php
/**
 * File: Schedule.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Schedule
 *
 * @package App
 * @property string $id
 * @property string $branch_id
 * @property string|null $classroom_id
 * @property string $course_id
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property int $duration In minutes
 * @property \Carbon\Carbon|null $monday
 * @property \Carbon\Carbon|null $tuesday
 * @property \Carbon\Carbon|null $wednesday
 * @property \Carbon\Carbon|null $thursday
 * @property \Carbon\Carbon|null $friday
 * @property \Carbon\Carbon|null $saturday
 * @property \Carbon\Carbon|null $sunday
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|Course|null $course
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereClassroomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereFriday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereMonday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereSaturday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereSunday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereThursday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereTuesday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereWednesday($value)
 * @mixin \Eloquent
 */
class Schedule extends Model
{
    use UsesUuid;

    public const TABLE = 'schedules';

    protected $table = self::TABLE;

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'monday' => 'time',
        'tuesday' => 'time',
        'wednesday' => 'time',
        'thursday' => 'time',
        'friday' => 'time',
        'saturday' => 'time',
        'sunday' => 'time',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Course|null
     */
    public function course(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
