<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\PayoutStatus;
use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property PayoutStatus $status
 * @property int|null $amount
 * @property string $branch_id
 * @property string $instructor_id
 * @property \Carbon\Carbon $period_from
 * @property \Carbon\Carbon $period_to
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo<Branch> $branch
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo<Instructor> $instructor
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsToMany<Lesson> $lessons
 * @mixin \Eloquent
 */
class Payout extends Model
{
    use UsesUuid;
    use HasFactory;

    public const TABLE = 'payouts';

    protected $table = self::TABLE;

    protected $casts = [
        'period_from' => 'date',
        'period_to' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => PayoutStatus::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Branch>
     */
    public function branch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Instructor>
     */
    public function instructor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_payout');
    }
}
