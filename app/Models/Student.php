<?php
/**
 * File: Student.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\StudentStatus;
use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Student
 *
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property ?string $card_number
 * @property StudentStatus $status [potential|active|recent|former]
 * @property string $person_id
 * @property string $customer_id
 * @property int|null $visits_count
 * @property int|null $subscriptions_count
 * @property \Carbon\Carbon $seen_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Customer $customer
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \App\Models\Person $person
 * @property-read HasMany<Subscription>|null $subscriptions
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
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Student onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Student withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Student withoutTrashed()
 */
class Student extends Model
{
    use HasRoles, SoftDeletes, UsesUuid, HasFactory;

    public const TABLE = 'students';

    protected $table = self::TABLE;

    protected $guarded = [];

    protected $casts = [
        'status' => StudentStatus::class,
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

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)->orderBy('created_at', 'desc');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class)->orderBy('created_at', 'desc');
    }
}
