<?php
/**
 * File: Account.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Branch;

/**
 * Class Account
 *
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property int $amount
 * @property string $type [personal|savings|operational]
 * @property string $owner_type [App\Models\Student|App\Models\Instructor|App\Models\Branch]
 * @property int $owner_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bonus[] $bonuses
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment[] $payments
 * @property-read \Illuminate\Database\Eloquent\Relations\MorphTo|Instructor|Student|Branch $owner
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Account onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Account withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Account withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereOwnerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Account extends Model
{
    use SoftDeletes;

    public const TABLE = 'accounts';

    public const TYPE_OPERATIONAL = 'operational';
    public const TYPE_SAVINGS = 'savings';
    public const TYPE_PERSONAL = 'personal';
    public const TYPES = [
        self::TYPE_OPERATIONAL,
        self::TYPE_SAVINGS,
        self::TYPE_PERSONAL
    ];

    public const OWNER_TYPES = [
        Student::class,
        Instructor::class,
        Branch::class
    ];

    protected $table = self::TABLE;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo|Instructor|Student|Branch
     */
    public function owner(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Payment[]
     */
    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Bonus[]
     */
    public function bonuses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Bonus::class);
    }
}
