<?php

declare(strict_types=1);

namespace App\Components\Shift;

use App\Common\BaseFormatter;

/**
 * @mixin \App\Models\Shift
 */
class Formatter extends BaseFormatter
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'total_income' => (int)$this->total_income,
            'transactions_count' => $this->transactions_count ? (int)$this->transactions_count : null,
            'transactions_grouped_by_account' => $this->whenLoaded(
                'transactions',
                fn () => $this->transactions_grouped_by_account,
            ),
            'transactions_grouped_by_transfer_type' => $this->whenLoaded(
                'transactions',
                fn () => $this->transactions_grouped_by_transfer_type,
            ),
            'transactions_grouped_by_type' => $this->whenLoaded(
                'transactions',
                fn () => $this->transactions_grouped_by_type,
            ),
            'status' => $this->status->value,
            'status_label' => translate('shift.status', $this->status),
            'branch_id' => $this->branch_id,
            'branch' => $this->whenLoaded(
                'branch',
                fn () => new \App\Components\Branch\FormatterInShift($this->branch),
            ),
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded(
                'user',
                fn () => new \App\Components\User\Formatter($this->user),
            ),
            'created_at' => $this->created_at->toDateTimeString(),
            'closed_at' => $this->closed_at?->toDateTimeString(),
        ];
    }
}
