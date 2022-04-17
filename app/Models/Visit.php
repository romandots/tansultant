<?php
/**
 * File: Visit.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */
declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Visit
 *
 * @package App\Models
 * @property string $id
 * @property int $lesson_id
 * @property int $student_id
 * @property int|null $manager_id
 * @property int|null $payment_id
 * @property int $event_id
 * @property string $payment_type
 * @property string $event_type
 * @property int|null $price
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Relations\MorphTo|\App\Models\Lesson $event
 * @property-read \App\Models\User $manager
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|Payment|null $payment
 * @property-read \App\Models\Student $student
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visit query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visit whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visit whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visit whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visit wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visit wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visit whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visit whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Visit extends Model
{
    use UsesUuid;
    use HasFactory;

    public const TABLE = 'visits';

    public const PAYMENT_TYPES = [
        Payment::class,
        'App\Models\Promocode',
    ];

    public const EVENT_TYPES = [
        Lesson::class,
        '\App\Models\Event'
    ];

    protected $table = self::TABLE;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Student|null
     */
    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class)->with('person');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo|\App\Models\Lesson
     */
    public function event(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|User|null
     */
    public function manager(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->with('person');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Payment|null
     */
    public function payment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
