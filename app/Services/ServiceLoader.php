<?php

namespace App\Services;

use App\Common\Locator;

class ServiceLoader extends Locator
{
    public static function price(): Price\PriceService {
        return self::get(Price\PriceService::class);
    }
}