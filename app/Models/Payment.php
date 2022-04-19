<?php
/**
 * File: Payment.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\PaymentObjectType;
use App\Models\Enum\PaymentStatus;
use App\Models\Enum\PaymentTransferType;
use App\Models\Enum\PaymentType;
use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Payment
 *
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property int $amount
 * @property PaymentType $type [manual|automatic]
 * @property PaymentTransferType $transfer_type [cash|card|online|internal|code]
 * @property PaymentStatus $status [pending|expired|confirmed|canceled]
 * @property PaymentObjectType $object_type [App\Models\Lesson|App\Models\Visit]
 * @property string|null $object_id
 * @property string $account_id
 * @property string|null $related_id
 * @property string|null $external_id
 * @property string $user_id
 * @property \Carbon\Carbon|null $confirmed_at
 * @property \Carbon\Carbon|null $canceled_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|Account $account
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|User $user
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|Payment|null $related_payment
 * @property-read \Illuminate\Database\Eloquent\Relations\MorphTo|Lesson|Visit $object
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereCanceledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereObjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereRelatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereTransferType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payment whereUserId($value)
 * @mixin \Eloquent
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Payment onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Payment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Payment withoutTrashed()
 */
class Payment extends Model
{
    use SoftDeletes;
    use UsesUuid;
    use HasFactory;

    public const TABLE = 'payments';

    protected $table = self::TABLE;

    protected $casts = [
        'type' => PaymentType::class,
        'transfer_type' => PaymentTransferType::class,
        'object_type' => PaymentObjectType::class,
        'status' => PaymentStatus::class,
        'expired_at' => 'datetime',
        'confirmed_at' => 'datetime',
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|User
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|self|null
     */
    public function related_payment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'related_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo|Lesson|Visit
     */
    public function object(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
