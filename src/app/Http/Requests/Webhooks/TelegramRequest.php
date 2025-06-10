<?php

namespace App\Http\Requests\Webhooks;

use App\Components\Loader;
use App\Models\Person;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;

class TelegramRequest extends \Illuminate\Foundation\Http\FormRequest
{
    public function rules(): array
    {
        return [
            'recipient' => [
                'string',
                'required',
            ],
            'action' => [
                'string',
                'required',
            ],
        ];
    }

    public function getAction(): string
    {
        return $this->input('action');
    }

    public function getPerson(): Person
    {
        $person = Loader::people()->getByTelegramUsername($this->input('recipient'));
        if (null !== $person) {
            return $person;
        }

        $person = Loader::people()->getByPhoneNumber($this->input('recipient'));
        if (null !== $person) {
            return $person;
        }

        throw new ModelNotFoundException('Person not found');
    }
}