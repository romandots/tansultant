<?php
/**
 * File: Course.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Course

 * @property string $id
 * @property string $name
 * @property string|null $summary
 * @property string|null $description
 * @property string|null $age_restrictions
 * @property string|null $picture
 * @property string|null $picture_thumb
 * @property string $status [pending|active|disabled]
 * @property string|null $instructor_id
 * @property \Carbon\Carbon|null $starts_at
 * @property \Carbon\Carbon|null $ends_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|Instructor|null $instructor
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
 * @mixin \Eloquent
 */
class Course extends Model
{
    use SoftDeletes;
    use UsesUuid;

    public const TABLE = 'courses';

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_DISABLED = 'disabled';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ACTIVE,
        self::STATUS_DISABLED
    ];

    protected $table = self::TABLE;

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Instructor|null
     */
    public function instructor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Instructor::class)->with('person');
    }
}
