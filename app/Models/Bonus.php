<?php
/**
 * File: Bonus.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\BonusStatus;
use App\Models\Enum\BonusType;
use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Bonus
 *
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property int $amount
 * @property BonusType $type [code|reward]
 * @property BonusStatus $status [pending | expired | activated | canceled]
 * @property string $account_id
 * @property string|null $promocode_id
 * @property int $user_id
 * @property \Carbon\Carbon|null $activated_at
 * @property \Carbon\Carbon|null $canceled_at
 * @property \Carbon\Carbon|null $expired_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus query()
 * @property-read \App\Models\Account $account
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus whereActivatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus whereCanceledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus wherePromocodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bonus whereUserId($value)
 * @mixin \Eloquent
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bonus onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bonus withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bonus withoutTrashed()
 */
class Bonus extends Model
{
    use SoftDeletes;
    use UsesUuid;
    use HasFactory;

    public const TABLE = 'bonuses';

    protected $table = self::TABLE;

    protected $casts = [
        'type' => BonusType::class,
        'status' => BonusStatus::class,
        'expired_at' => 'datetime',
        'activated_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Account
     */
    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|User|null
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->with('person');
    }

//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Promocode|null
//     */
//    public function promocode(): \Illuminate\Database\Eloquent\Relations\BelongsTo
//    {
//        return $this->belongsTo(Promocode::class);
//    }
}
