<?php

declare(strict_types=1);

namespace App\Components\Payout;

use App\Common\BaseComponentFacade;
use Illuminate\Support\Collection;

class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function createBatch(StoreBatchDto $dto): Collection
    {
        return $this->getService()->createBatch($dto);
    }

    public function attachLessons(DtoLessons $payoutLessons): void
    {
        $this->getService()->updateLessons($payoutLessons);
    }

    public function detachLessons(DtoLessons $payoutLessons): void
    {
        $this->getService()->deleteLessons($payoutLessons);
    }

    public function transitionBatch(UpdateBatchDto $batch): void
    {
        $this->getService()->transitionBatch($batch);
    }

    public function deleteBatch(UpdateBatchDto $batch): void
    {
        $this->getService()->deleteBatch($batch);
    }

    public function checkoutBatch(CheckoutBatchDto $getDto): void
    {
        $this->getService()->checkoutBatch($getDto);
    }

    public function generatePayoutReport(\App\Models\Payout $payout): void
    {
        $this->getService()->generatePayoutReport($payout);
    }
}