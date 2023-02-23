<?php

declare(strict_types=1);

namespace App\Components\Formula;

use App\Common\BaseComponentService;
use App\Events\Formula\FormulaEvent;
use App\Models\Formula;

/**
 * @method Repository getRepository()
 */
class Service extends BaseComponentService
{
    protected const ALIAS_MAP = [
        'П' => 'V',
        'А' => 'S',
        'Б' => 'F',
    ];
    protected const DESCRIPTIONS = [
        'V' => 'посещения урока',
        'S' => 'активные подписки на курс',
        'F' => 'бесплатные посещения урока',
    ];

    public function __construct()
    {
        parent::__construct(
            Formula::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    protected function prepareEquation(string $equation): string
    {
        $trimmed = preg_replace('/\s/', '', mb_strtoupper(trim($equation)));
        return str_replace(array_keys(self::ALIAS_MAP), array_values(self::ALIAS_MAP), $trimmed);
    }

    public function describeEquation(?string $equation): string
    {
        if (null === $equation) {
            return '';
        }

        $equation = $this->prepareEquation($equation);
        $equation = preg_replace('/([+\-*\/])/', " \\1 ", $equation);

        return str_replace(
            array('*', ...array_keys(self::DESCRIPTIONS)),
            array('×', ...array_values(self::DESCRIPTIONS)),
            $equation
        );
    }
}