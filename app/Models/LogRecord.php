<?php
/**
 * File: LogRecord.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-10
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Models;

use App\Models\Enum\LogRecordAction;
use App\Models\Enum\LogRecordObjectType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\LogRecord
 *
 * @property int $id
 * @property LogRecordAction $action
 * @property LogRecordObjectType $object_type
 * @property string $object_id
 * @property string $user_id
 * @property string $message
 * @property object|null $old_value
 * @property object|null $new_value
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogRecord whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogRecord whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogRecord whereNewValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogRecord whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogRecord whereObjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogRecord whereOldValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LogRecord whereUserId($value)
 * @mixin \Eloquent
 */
class LogRecord extends Model
{
    public const TABLE = 'log_records';

    public const UPDATED_AT = null;

    public $table = self::TABLE;

    public $casts = [
        'action' => LogRecordAction::class,
        'object_type' => LogRecordObjectType::class,
        'old_value' => 'object',
        'new_value' => 'object',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo($this->object_type->value, 'object_id', 'id');
    }
}
