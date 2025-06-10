<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Models
 * @property string $id
 * @property string $customer_id
 * @property string|null $transaction_id
 * @property string $name
 * @property int $amount
 * @property \Carbon\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo<Customer> $customer
 * @property-read \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Transaction>|null $transaction
 * @mixin \Eloquent
 */
class Credit extends Model
{
    use UsesUuid;
    //use HasFactory;

    public const TABLE = 'credits';
    public const UPDATED_AT = null;

    protected $table = self::TABLE;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Transaction>
     */
    public function transaction(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this
            ->belongsTo(\App\Models\Transaction::class)
            ->with(['account']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Customer>
     */
    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class)->with('person');
    }

//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Promocode|null
//     */
//    public function promocode(): \Illuminate\Database\Eloquent\Relations\BelongsTo
//    {
//        return $this->belongsTo(Promocode::class);
//    }
}
