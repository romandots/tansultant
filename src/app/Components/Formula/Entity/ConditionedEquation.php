<?php

namespace App\Components\Formula\Entity;

use App\Components\Formula\Exceptions\InvalidFormulaEquationException;
use App\Components\Formula\Exceptions\InvalidVariableInFormulaEquationException;
use App\Models\Enum\FormulaVar;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ConditionedEquation
{
    protected ExpressionLanguage $expressionLanguage;

    public function __construct(
        readonly public string $equation,
        readonly public ?string $condition = null,
        readonly public array $values = [],
    )
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public static function create(string $conditionEquationString, array $values = []): self
    {
        $conditionEquationString = trim($conditionEquationString);
        $matches = [];
        if (!preg_match('/\[(.*)](.*)/', $conditionEquationString, $matches)) {
            $conditionEquationString = str_replace(['[', ']'], '', $conditionEquationString);
            return new self($conditionEquationString, null, $values);
        }

        return new self(trim($matches[2]), trim($matches[1]), $values);
    }

    public function evaluateCondition(): bool
    {
        if (null === $this->condition) {
            return true;
        }

        return (bool)$this->expressionLanguage->evaluate($this->condition);
    }

    public function evaluateEquation(): float
    {
        return (float)$this->expressionLanguage->evaluate($this->equation, $this->values);
    }

    public function describeEquation(): string
    {
        $equation = $this->trimEquation($this->equation);
        $equation = preg_replace('/([+\-*\/])/', " \\1 ", $equation);
        $search = ['*', ...FormulaVar::values()];
        $replace = ['×', ...FormulaVar::descriptions()];
        $map = array_combine(
            $search,
            $replace
        );

        return preg_replace_callback('/'.implode('|', array_map('preg_quote', $search)).'/',
            static fn ($matches) => $map[$matches[0]],
            $equation
        );
    }

    public function describeCondition(): string
    {
        if (null === $this->condition) {
            return '';
        }

        $condition = $this->trimEquation($this->condition);
        $condition = preg_replace('/([<>=&])/', " \\1 ", $condition);

        return str_replace(
            array('*', ...array_keys(FormulaVar::values())),
            array('×', ...array_values(FormulaVar::descriptions())),
            $condition
        );
    }

    public function checkEquation(): void
    {
        preg_match_all('/[\p{Latin}\p{Cyrillic}_]+/ui', $this->equation, $matches);
        $variablesUsed = array_unique($matches[0]);
        $validVariables = FormulaVar::values();

        foreach ($variablesUsed as $variable) {
            if (!in_array($variable, $validVariables, true)) {
                throw new InvalidVariableInFormulaEquationException($variable);
            }
        }

        try {
            $this->expressionLanguage->parse($this->equation, $validVariables);
            if ($this->condition) {
                $this->expressionLanguage->parse($this->condition, $validVariables);
            }
        } catch (\Symfony\Component\ExpressionLanguage\SyntaxError $e) {
            throw new InvalidFormulaEquationException();
        }
    }

    protected function trimEquation(string $equation): string
    {
        return preg_replace('/\s/', '', mb_strtoupper(trim($equation)));
    }
}