<?php

declare(strict_types=1);

namespace App\Components\Payout;

use App\Common\BaseComponentService;
use App\Common\Contracts;
use App\Components\Loader;
use App\Models\Payout;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends BaseComponentService
{

    public function __construct()
    {
        parent::__construct(
            Payout::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    public function create(Contracts\DtoWithUser $dto): Model
    {
        assert($dto instanceof Dto);
        $dto->name = $this->generatePayoutName($dto);
        return parent::create($dto);
    }

    private function generatePayoutName(Dto $dto): string
    {
        return \trans('payout.name', [
            'period' => $this->getPeriodAsString($dto->period_from, $dto->period_to),
            'branch' => Loader::branches()->findById($dto->branch_id)->name,
            'instructor' => Loader::instructors()->findById($dto->instructor_id)->name,
        ]);
    }

    private function getPeriodAsString(\Carbon\Carbon $periodFrom, \Carbon\Carbon $periodTo): string
    {
        if ($periodTo->year !== $periodFrom->year) {
            return sprintf('%s − %s', $periodFrom->toFormattedDateString(), $periodTo->toFormattedDateString());
        }

        if ($periodTo->month !== $periodFrom->month) {
            return sprintf('%s − %s', $periodFrom->format('M j'), $periodTo->toFormattedDateString());
        }

        return sprintf('%d−%d %s %d', $periodFrom->day, $periodTo->day, $periodFrom->shortMonthName, $periodFrom->year);
    }

}