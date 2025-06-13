<?php
/**
 * File: User.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\UserStatus;
use App\Models\Enum\UserType;
use App\Models\Traits\HasName;
use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * @package App
 * @property string $id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string $person_id
 * @property string|null $active_shift_id
 * @property UserStatus $status
 * @property UserType $type
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $approved_at
 * @property \Carbon\Carbon|null $seen_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read \App\Models\Person $person
 * @property-read \App\Models\Instructor $instructor
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\Student $student
 * @property-read BelongsTo|Shift $active_shift
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User role($roles, $guard = null)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read int|null $notifications_count
 * @property-read int|null $permissions_count
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUsername($value)
 */
class User extends Authenticatable
{
    use HasName;
    use HasRoles;
    use HasPermissions;
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use Notifiable;
    use UsesUuid;

    public const TABLE = 'users';

    protected $table = self::TABLE;

    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'status' => UserStatus::class,
        'created_at' => 'datetime',
        'approved_at' => 'datetime',
        'seen_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Person|null
     */
    public function person(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Person::class)->with('students', 'customers', 'instructors');
    }

    /**
     * @return Customer|null
     */
    public function getCustomerAttribute(): ?Customer
    {
        return $this->person ? $this->person->customer : null;
    }

    /**
     * @return Student|null
     */
    public function getStudentAttribute(): ?Student
    {
        return $this->person ? $this->person->student : null;
    }

    /**
     * @return Instructor|null
     */
    public function getInstructorAttribute(): ?Instructor
    {
        return $this->person ? $this->person->instructor : null;
    }

    public function tokens()
    {
        return $this->morphMany(Sanctum::$personalAccessTokenModel, 'tokenable', "tokenable_type", "tokenable_uuid");
    }

    /**
     * @return BelongsTo<Shift>
     */
    public function active_shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'active_shift_id');
    }

    public function isAdmin(): bool
    {
        $adminRole = Role::findByName(\App\Services\Permissions\UserRoles::ADMIN);
        return $this->hasRole($adminRole);
    }
}
