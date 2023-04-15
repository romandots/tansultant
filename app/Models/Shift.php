<?php

declare(strict_types=1);

namespace App\Models;

use App\Components\Loader;
use App\Models\Enum\ShiftStatus;
use App\Models\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property int|null $total_income
 * @property HasMany|Collection|Transaction[] $transactions
 * @property array $transactions_by_transfer_type
 * @property int|null $transactions_count
 * @property array $transactions_grouped_by_transfer_type
 * @property array $transactions_grouped_by_type
 * @property array $transactions_grouped_by_account
 * @property ShiftStatus $status
 * @property string $user_id
 * @property string|null $branch_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $closed_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Branch $branch
 * @mixin \Eloquent
 */
class Shift extends Model
{
    use UsesUuid;

    //use HasFactory;

    public const TABLE = 'shifts';

    protected $table = self::TABLE;

    protected $casts = [
        'status' => ShiftStatus::class,
        'closed_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User>|null
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->with('person');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Branch>|null
     */
    public function branch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return HasMany<Transaction>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getTransactionsByTransferTypes(): Collection
    {
        return $this->transactions->groupBy(fn ($item) => $item->transfer_type->value);
    }

    public function getTransactionsByTypes(): Collection
    {
        return $this->transactions->groupBy(fn ($item) => $item->type->value);
    }

    public function getTransactionsByAccounts(): Collection
    {
        return $this->transactions->groupBy(fn ($item) => $item->account_id);
    }

    public function getTransactionsGroupedByTransferTypeAttribute(): array
    {
        $transactions = $this->getTransactionsByTransferTypes();
        $details = [];
        foreach ($transactions as $transferType => $group) {
            $details[] = [
                'transfer_type' => $transferType,
                'transfer_type_label' => translate('transaction.transfer_type', $transferType),
                'sum' => $group->sum('amount'),
                'count' => $group->count(),
            ];
        }

        return $details;
    }

    public function getTransactionsGroupedByTypeAttribute(): array
    {
        $transactions = $this->getTransactionsByTypes();
        $details = [];
        foreach ($transactions as $type => $group) {
            $details[] = [
                'type' => $type,
                'type_label' => translate('transaction.type', $type),
                'sum' => $group->sum('amount'),
                'count' => $group->count(),
            ];
        }

        return $details;
    }

    public function getTransactionsGroupedByAccountAttribute(): array
    {
        $transactions = $this->getTransactionsByAccounts();
        $details = [];
        foreach ($transactions as $accountId => $group) {
            $account = Loader::accounts()->findById($accountId);
            $details[] = [
                'account_id' => $account->id,
                'account_name' => $account->name,
                'sum' => $group->sum('amount'),
                'count' => $group->count(),
            ];
        }

        return $details;
    }

    public function isClosed(): bool
    {
        return $this->status === ShiftStatus::CLOSED;
    }
}
