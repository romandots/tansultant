<?php

namespace App\Components\Course\Exceptions;

use App\Exceptions\InvalidStatusException;
use App\Models\Enum\TariffStatus;
use App\Models\Tariff;

class CannotAttachArchivedTariff extends InvalidStatusException
{
    public function __construct(public readonly Tariff $tariff)
    {
        parent::__construct($this->tariff->status->value, [TariffStatus::ACTIVE]);
    }
}