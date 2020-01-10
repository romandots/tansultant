<?php
/**
 * File: VerificationCode.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VerificationCode
 *
 * @package App\Models
 * @property string $id
 * @property string $phone_number
 * @property string $verification_code
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $expired_at
 * @property \Carbon\Carbon|null $verified_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode whereVerificationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VerificationCode whereVerifiedAt($value)
 * @mixin \Eloquent
 */
class VerificationCode extends Model
{
    use UsesUuid;

    public const TABLE = 'verification_codes';

    protected $table = self::TABLE;

    public const UPDATED_AT = null;
}
