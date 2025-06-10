<?php

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Payout\DtoLessons;
use App\Models\Formula;
use App\Models\Lesson;
use Illuminate\Validation\Rule;

class UpdatePayoutLessonsRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'formula_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Formula::TABLE, 'id'),
            ],
            'lessons_ids' => [
                'required',
                'array',
            ],
            'lessons_ids.*' => [
                'required_with:lessons_ids',
                'string',
                'uuid',
                Rule::exists(Lesson::TABLE, 'id'),
            ],
        ]);
    }

    public function getDto(): DtoLessons
    {
        $validated = $this->validated();
        $dto = new DtoLessons($this->user());

        $dto->payout_id = $this->getId();
        $dto->formula_id = $validated['formula_id'];
        $dto->lessons_ids = $validated['lessons_ids'] ?? [];

        return $dto;
    }
}
