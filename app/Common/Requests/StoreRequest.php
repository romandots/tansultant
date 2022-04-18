<?php

namespace App\Common\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class StoreRequest extends FormRequest
{
    abstract public function getDto(): \App\Common\Contracts\DtoWithUser;
}