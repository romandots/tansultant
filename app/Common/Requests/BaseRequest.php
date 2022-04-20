<?php

namespace App\Common\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    abstract public function rules(): array;
    abstract public function getDto(): \App\Common\Contracts\DtoWithUser;
}