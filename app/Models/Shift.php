<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\ShiftStatus;
use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property float|null $total_income
 * @property ShiftStatus $status
 * @property string $user_id
 * @property string|null $branch_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $closed_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 */
class Shift extends Model
{
    use UsesUuid;
    //use HasFactory;

    public const TABLE = 'shifts';

    protected $table = self::TABLE;

    protected $casts = [
        'status' => ShiftStatus::class,
        'closed_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User>|null
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->with('person');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Branch>|null
     */
    public function branch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
