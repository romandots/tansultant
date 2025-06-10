<?php

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Common\Requests\StoreRequest;
use App\Components\Payout\Dto;
use App\Models\Branch;
use App\Models\Enum\PayoutStatus;
use App\Models\Instructor;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class StorePayoutRequest extends StoreRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            'name' => [
                'nullable',
                'string',
            ],
            'branch_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Branch::TABLE, 'id'),
            ],
            'instructor_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists(Instructor::TABLE, 'id'),
            ],
            'period_from' => [
                'required',
                'string',
                'date',
            ],
            'period_to' => [
                'required',
                'string',
                'date',
            ],
        ]);
    }

    public function getDto(): Dto
    {
        $validated = $this->validated();
        $dto = new Dto($this->user());

        $dto->name = $validated['name'] ?? null;
        $dto->branch_id = $validated['branch_id'];
        $dto->instructor_id = $validated['instructor_id'];
        $dto->period_from = Carbon::parse($validated['period_from']);
        $dto->period_to = Carbon::parse($validated['period_to']);
        $dto->status = PayoutStatus::CREATED;

        return $dto;
    }
}
