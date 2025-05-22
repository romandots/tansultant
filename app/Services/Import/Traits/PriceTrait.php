<?php

namespace App\Services\Import\Traits;

use App\Components\Loader;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;

trait PriceTrait
{

    protected function getPriceId(int $priceValue, ImportContext $ctx): string
    {
        try {
            $price = Loader::prices()->findByPriceValue($priceValue);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            try {
                $priceDto = new \App\Components\Price\Dto();
                $priceDto->name = $priceValue;
                $priceDto->price = $priceValue;
                $price = Loader::prices()->create($priceDto);
                $ctx->manager->increaseCounter('price');
            } catch (\Exception $e) {
                throw new ImportException("Ошибка создания цены: {$e->getMessage()}");
            }
        }
        return $price->id;
    }
}