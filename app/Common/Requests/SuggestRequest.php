<?php

namespace App\Common\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuggestRequest extends FormRequest
{
    public function rules(): array
    {
        return [];
    }

    public function getQuery(): ?string
    {
        return $this->query('query');
    }
}