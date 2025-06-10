<?php

namespace App\Components\Account\Exceptions;

use App\Components\Account\Formatter;
use App\Components\Loader;
use App\Exceptions\AlreadyExistsException;

class AccountAlreadyExists extends AlreadyExistsException
{
    protected \App\Models\Account $account;

    public function __construct(\App\Models\Account $account)
    {
        $formattedAccount = Loader::accounts()->format($account->load('branch'), Formatter::class);
        parent::__construct($formattedAccount);
        $this->account = $account;
    }

    /**
     * @return \App\Models\Account
     */
    public function getAccount(): \App\Models\Account
    {
        return $this->account;
    }
}