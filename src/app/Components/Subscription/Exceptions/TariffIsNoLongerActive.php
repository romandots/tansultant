<?php

namespace App\Components\Subscription\Exceptions;

use App\Components\Loader;
use App\Components\Tariff\Formatter;
use App\Exceptions\BaseException;
use App\Models\Tariff;

class TariffIsNoLongerActive extends BaseException
{
    public function __construct(
        protected readonly ?Tariff $tariff
    ) {
        parent::__construct('tariff_is_no_longer_active', [
            'tariff' => $tariff ? Loader::tariffs()->format($this->tariff, Formatter::class) : null,
        ]);
    }
}