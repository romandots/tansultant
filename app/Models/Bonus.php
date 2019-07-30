<?php
/**
 * File: Bonus.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Bonus
 *
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property int $amount
 * @property string $type [code|reward]
 * @property string $status [pending | expired | activated | canceled]
 * @property string $account_id
 * @property int|null $promocode_id
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
 * @mixin \Eloquent
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
 */
class Bonus extends Model
{
    public const TABLE = 'bonuses';

    public const TYPE_CODE = 'code';
    public const TYPE_REWARD = 'reward';
    public const TYPES = [
        self::TYPE_CODE,
        self::TYPE_REWARD
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_ACTIVATED = 'activated';
    public const STATUS_CANCELED = 'canceled';
    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_EXPIRED,
        self::STATUS_ACTIVATED,
        self::STATUS_CANCELED,
    ];

    protected $table = self::TABLE;

    protected $casts = [
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
