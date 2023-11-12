<?php
/**
 * File: Instructor.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use App\Models\Contracts\HasUniqueFields;
use App\Models\Enum\InstructorStatus;
use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Instructor
 *
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $picture
 * @property bool $display
 * @property InstructorStatus $status
 * @property string $person_id
 * @property \Carbon\Carbon|null $seen_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
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
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Instructor onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Instructor withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Instructor withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Instructor whereDeletedAt($value)
 */
class Instructor extends Model implements HasUniqueFields
{
    use HasRoles;
    use UsesUuid;
    use HasFactory;

    public const TABLE = 'instructors';

    protected $table = self::TABLE;

    protected $guarded = [];

    public $timestamps = [
        'created_at',
        'updated_at',
        'seen_at',
        'deleted_at'
    ];

    public $casts = [
        'status' => InstructorStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'seen_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Person|null
     */
    public function person(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function uniqueFields(): array
    {
        return [
            'name',
        ];
    }
}
