<?php

namespace App\Adapters;

abstract class Client
{
    abstract public function externalSystemName(): string;
}