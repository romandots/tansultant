<?php
/**
 * File: User.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 * @package App
 * @property string $id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string $person_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $seen_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read \App\Models\Person $person
 * @property-read \App\Models\Instructor $instructor
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\Student $student
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User role($roles, $guard = null)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable, HasRoles, HasApiTokens, Notifiable, UsesUuid;

    public const TABLE = 'users';

    protected $table = self::TABLE;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'approved_at' => 'datetime',
        'seen_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Person|null
     */
    public function person(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Person::class)->with('student', 'customer', 'instructor');
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

    /**
     * Find the user instance for the given username.
     * @param string $username
     * @return \Illuminate\Database\Eloquent\Model|User|null
     */
    public function findForPassport($username): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->where('username', $username)->first();
    }
}
