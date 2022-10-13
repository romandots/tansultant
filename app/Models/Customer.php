<?php
/**
 * File: Customer.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Customer
 *
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property string $person_id
 * @property \Carbon\Carbon $seen_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int|null $students_count
 * @property int|null $credits_sum
 * @property int|null $bonuses_sum
 * @property int|null $pending_bonuses_sum
 * @property-read \App\Models\Contract $contract
 * @property-read BelongsTo<Person> $person
 * @property-read HasMany<Student> $students
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read int|null $permissions_count
 * @property-read int|null $roles_count
 */
class Customer extends Model
{
    use HasRoles;
    use SoftDeletes;
    use UsesUuid;
    use HasFactory;

    public const TABLE = 'customers';

    protected $table = self::TABLE;

    protected $guarded = [];

    protected $casts = [
        'seen_at' => 'date'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Person|null
     */
    public function person(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasOne|Contract|null
     */
    public function contract(): \Illuminate\Database\Eloquent\Relations\hasOne
    {
        return $this->hasOne(Contract::class);
    }

    /**
     * @return HasMany<Student>
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
