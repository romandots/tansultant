<?php
/**
 * File: Branch.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Branch
 *
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property string|null $summary
 * @property string|null $description
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $url
 * @property string|null $vk_url
 * @property string|null $facebook_url
 * @property string|null $telegram_username
 * @property string|null $instagram_username
 * @property object|null $address
 * @property int|null $number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch whereFacebookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch whereInstagramUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch whereTelegramUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Branch whereVkUrl($value)
 * @mixin \Eloquent
 */
class Branch extends Model
{
    use UsesUuid;

    public const TABLE = 'branches';

    public const ADDRESS_JSON = [
        'country',
        'city',
        'street',
        'building',
        'coordinates'
    ];

    protected array $casts = [
        'address' => 'array'
    ];

    protected string $table = self::TABLE;
}
