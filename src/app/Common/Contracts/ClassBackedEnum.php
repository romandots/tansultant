<?php

namespace App\Common\Contracts;

interface ClassBackedEnum
{
    public function getClass(): string;
}