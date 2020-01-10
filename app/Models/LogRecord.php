<?php
/**
 * File: LogRecord.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-10
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\LogRecord
 *
 * @property int $id
 * @property string $action
 * @property string $object_type
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

    public const OBJECTS = [
        \App\Models\Course::class,
        \App\Models\User::class,
        \App\Models\Person::class,
        \App\Models\Instructor::class,
        \App\Models\Student::class,
        \App\Models\Customer::class,
        \App\Models\Account::class,
        \App\Models\Classroom::class,
        \App\Models\Contract::class,
        \App\Models\Genre::class,
        \App\Models\Intent::class,
        \App\Models\Lesson::class,
        \App\Models\Payment::class,
        \App\Models\Person::class,
        \App\Models\Schedule::class,
        \App\Models\Student::class,
        \App\Models\Visit::class,
    ];

    public const ACTIONS = [
        self::ACTION_CREATE,
        self::ACTION_UPDATE,
        self::ACTION_DELETE,
        self::ACTION_RESTORE,
        self::ACTION_ENABLE,
        self::ACTION_DISABLE,
        self::ACTION_SEND,
    ];

    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';
    public const ACTION_RESTORE = 'restore';
    public const ACTION_ENABLE = 'enable';
    public const ACTION_DISABLE = 'disable';
    public const ACTION_SEND = 'send';

    public const UPDATED_AT = null;

    public $table = self::TABLE;

    public $casts = [
        'old_value' => 'object',
        'new_value' => 'object',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo($this->object_type, 'object_id', 'id');
    }
}
