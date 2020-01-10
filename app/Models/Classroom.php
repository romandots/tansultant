<?php
/**
 * File: Classroom.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-31
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Classroom
 *
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property string $branch_id
 * @property string|null $color
 * @property int|null $capacity
 * @property int|null $number
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo|Branch $branch
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Classroom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Classroom newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Classroom query()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Classroom whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Classroom whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Classroom whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Classroom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Classroom whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Classroom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Classroom whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Classroom whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Classroom whereUpdatedAt($value)
 */
class Classroom extends Model
{
    use UsesUuid;

    public const TABLE = 'classrooms';

    protected $table = self::TABLE;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Branch
     */
    public function branch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
