<?php

namespace App\Http\Requests\PublicApi;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class LessonsOnDateRequest extends FormRequest
{
    public function rules(): array
    {
        return [];
    }

    public function getDto(): DTO\LessonsOnDate
    {
        $dto = new DTO\LessonsOnDate();
        $dto->date = Carbon::parse($this->getDate());

        return $dto;
    }

    private function getDate(): string
    {
        return $this->route()?->parameter('date');
    }
}