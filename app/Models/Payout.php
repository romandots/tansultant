<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\PayoutStatus;
use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property PayoutStatus $status
 * @property int|null $amount
 * @property string $branch_id
 * @property string $instructor_id
 * @property string|null $transaction_id
 * @property string|null $report_link
 * @property int $lessons_count
 * @property \Carbon\Carbon $period_from
 * @property \Carbon\Carbon $period_to
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $paid_at
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|Branch|null $branch
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|Instructor|null $instructor
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsToMany|Collection|Lesson[]|null $lessons
 * @mixin \Eloquent
 */
class Payout extends Model implements HasMedia
{
    use UsesUuid;
    use HasFactory;
    use InteractsWithMedia;

    public const TABLE = 'payouts';
    public const MEDIA_COLLECTION = 'documents';

    protected $table = self::TABLE;

    protected $casts = [
        'period_from' => 'date',
        'period_to' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'paid_at' => 'datetime',
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
        return $this
            ->belongsToMany(Lesson::class, 'payout_has_lessons')
            ->withPivot([
                'amount', 'equation', 'formula_id',
            ]);
    }

    public function getReportLinkAttribute(): ?string
    {
        return $this->getMedia(self::MEDIA_COLLECTION)->first()?->getUrl();
    }
}
