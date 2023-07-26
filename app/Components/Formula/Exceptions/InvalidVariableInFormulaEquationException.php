<?php

namespace App\Components\Formula\Exceptions;

class InvalidVariableInFormulaEquationException extends Exception
{
    public function __construct(string $invalidVariable) {
        parent::__construct('invalid_variable_in_formula_equation', [
            'invalid_variable' => $invalidVariable
        ], 400);
    }
}