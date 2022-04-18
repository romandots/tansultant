<?php

namespace App\Common\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class UpdateRequest extends FormRequest
{
    abstract public function getDto(): \App\Common\Contracts\Dto;
}