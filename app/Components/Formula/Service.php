<?php

declare(strict_types=1);

namespace App\Components\Formula;

use App\Common\BaseComponentService;
use App\Models\Formula;
use App\Models\Lesson;

/**
 * @method Repository getRepository()
 */
class Service extends BaseComponentService
{
    protected const ALIAS_MAP = [
        'П' => 'V',
        'А' => 'S',
        'Б' => 'F',
        'Ч' => 'H',
        'М' => 'M',
    ];

    protected const DESCRIPTIONS = [
        'V' => 'посещения урока',
        'S' => 'активные подписки на курс',
        'F' => 'бесплатные посещения урока',
        'H' => 'часы',
        'M' => 'минуты',
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

    public function calculateLessonPayoutAmount(Lesson $lesson, Formula $formula): float
    {
        $substitutions = $this->getSubstitutionsForLesson($lesson);
        $equation = str_replace(
            \array_keys($substitutions),
            \array_values($substitutions),
            $this->prepareEquation($formula->equation)
        );

        return round($this->calculate($equation), 2);
    }

    protected function calculate(string $equation): float
    {
        return (float)eval("return {$equation};");
    }

    protected function getSubstitutionsForLesson(Lesson $lesson): array
    {
        return [
            'V' => (int)$lesson->visits_count,
            'S' => (int)$lesson->course->subscriptions_count,
            'F' => (int)0,
            'H' => (int)$lesson->getPeriodInHours(),
            'M' => (int)$lesson->getPeriodInMinutes(),
        ];
    }
}