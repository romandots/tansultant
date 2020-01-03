<?php
/**
 * File: Intent.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Intent
 *
 * @package App\Models
 * @property string $id
 * @property string $event_id
 * @property string $student_id
 * @property string|null $manager_id
 * @property string $event_type
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Relations\MorphTo|\App\Models\Lesson $event
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|User|null $manager
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|Student|null $student
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Intent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Intent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Intent query()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Intent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Intent whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Intent whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Intent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Intent whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Intent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Intent whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Intent whereUpdatedAt($value)
 */
class Intent extends Model
{
    use UsesUuid;

    public const TABLE = 'intents';

    public const EVENT_TYPES = [
        Lesson::class,
        '\App\Models\Event'
    ];

    public const STATUS_EXPECTING = 'expecting';
    public const STATUS_VISITED = 'visited';
    public const STATUS_NOSHOW = 'no-show';

    public const STATUSES = [
        self::STATUS_EXPECTING,
        self::STATUS_VISITED,
        self::STATUS_NOSHOW,
    ];

    protected string $table = self::TABLE;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo|\App\Models\Lesson
     */
    public function event(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Student|null
     */
    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|User|null
     */
    public function manager(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
