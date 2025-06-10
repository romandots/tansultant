<?php

declare(strict_types=1);

namespace App\Components\Branch;

use App\Common\BaseFormatter;
use App\Models\Enum\TransactionTransferType;

/**
 * @mixin \App\Models\Branch
 */
class FormatterInShift extends BaseFormatter
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
            'summary' => $this->summary,
            'cash_account' => $this->cash_account,
            'card_account' => $this->card_account,
            'online_account' => $this->online_account,
            'accounts_by_transfer_types' => $this->getAccountsByTransferTypes(),
        ];
    }

    private function getAccountsByTransferTypes(): array
    {
        $accounts = [];
        foreach (TransactionTransferType::cases() as $transferType) {
            $accounts[$transferType->value] = $this->getDefaultAccount($transferType);
        }
        return $accounts;
    }
}
