<?php

declare(strict_types=1);

namespace App\Components\Formula;

use App\Common\BaseFormatter;
use App\Components\Loader;

/**
 * @mixin \App\Models\Formula
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
            'equation' => $this->equation,
            'equation_description' => Loader::formulas()->describeFormulaEquation($this->equation),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
        ];
    }
}
