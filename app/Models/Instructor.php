<?php
/**
 * File: Instructor.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Instructor
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $picture
 * @property bool $display
 * @property string $status [hired|freelance|fired]
 * @property int $person_id
 * @property \Carbon\Carbon $seen_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \App\Models\Person $person
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor role($roles, $guard = null)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor whereDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor wherePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor whereSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor whereUpdatedAt($value)
 */
class Instructor extends Model
{
    use HasRoles;

    public const TABLE = 'instructors';

    public const STATUS_HIRED = 'hired';
    public const STATUS_FREELANCE = 'freelance';
    public const STATUS_FIRED = 'fired';
    public const STATUSES = [
        self::STATUS_HIRED,
        self::STATUS_FREELANCE,
        self::STATUS_FIRED
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
}
