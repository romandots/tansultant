<?php

namespace App\Components\Formula\Exceptions;

class InvalidFormulaEquationException extends Exception
{
    public function __construct() {
        parent::__construct('invalid_formula_equation', [], 400);
    }
}