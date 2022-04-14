<?php
/**
 * File: Contract.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Contract
 *
 * @todo add contract full text to record
 *
 * @package App\Models
 * @property string $id
 * @property string $serial
 * @property string $number
 * @property string $branch_id
 * @property string|null $customer_id
 * @property string $status [pending|signed|terminated]
 * @property \App\Models\Customer $customer
 * @property \Carbon\Carbon $signed_at
 * @property \Carbon\Carbon $terminated_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract query()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereSerial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereSignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereTerminatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereUpdatedAt($value)
 */
class Contract extends Model
{
    use UsesUuid;
    use HasFactory;

    public const TABLE = 'contracts';

    public const STATUS_PENDING = 'pending';
    public const STATUS_SIGNED = 'signed';
    public const STATUS_TERMINATED = 'terminated';
    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_SIGNED,
        self::STATUS_TERMINATED
    ];

    protected $table = self::TABLE;

    protected $fillable = [];

    protected $casts = [
        'signed_at' => 'datetime',
        'terminated_at' => 'datetime',
    ];

    /**
     * @return Contract|\Illuminate\Database\Eloquent\Relations\BelongsTo|Customer
     */
    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class)->with('person');
    }
}
