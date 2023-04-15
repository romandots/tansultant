<?php
/**
 * File: Transaction.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\TransactionStatus;
use App\Models\Enum\TransactionTransferType;
use App\Models\Enum\TransactionType;
use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property int $amount
 * @property TransactionType $type [manual|automatic]
 * @property TransactionTransferType $transfer_type [cash|card|online|internal|code]
 * @property TransactionStatus $status [pending|expired|confirmed|canceled]
 * @property string $account_id
 * @property string|null $shift_id
 * @property string|null $customer_id
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
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|Shift|null $shift
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|Transaction|null $related_payment
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|Customer|null $customer
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereCanceledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereObjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereRelatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereTransferType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereUserId($value)
 * @mixin \Eloquent
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Transaction withoutTrashed()
 */
class Transaction extends Model
{
    use SoftDeletes;
    use UsesUuid;
    use HasFactory;

    public const TABLE = 'transactions';

    protected $table = self::TABLE;

    protected $casts = [
        'type' => TransactionType::class,
        'transfer_type' => TransactionTransferType::class,
        'status' => TransactionStatus::class,
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
    public function related(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'related_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Customer>
     */
    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo<Shift>
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
