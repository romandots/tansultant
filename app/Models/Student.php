<?php
/**
 * File: Student.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Student
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property int $card_number
 * @property string $status [potential|active|recent|former]
 * @property int $person_id
 * @property int $customer_id
 * @property \Carbon\Carbon $seen_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Customer $customer
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \App\Models\Person $person
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student role($roles, $guard = null)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereUpdatedAt($value)
 */
class Student extends Model
{
    use HasRoles, SoftDeletes;

    public const TABLE = 'students';

    public const STATUS_POTENTIAL = 'potential';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_RECENT = 'recent';
    public const STATUS_FORMER = 'former';
    public const STATUSES = [
        self::STATUS_POTENTIAL,
        self::STATUS_ACTIVE,
        self::STATUS_RECENT,
        self::STATUS_FORMER
    ];

    protected $table = self::TABLE;

    protected $guarded = [];

    protected $casts = [
        'seen_at' => 'datetime'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Person|null
     */
    public function person(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Customer|null
     */
    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
