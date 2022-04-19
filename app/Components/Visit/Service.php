<?php

declare(strict_types=1);

namespace App\Components\Visit;

use App\Components\Loader;
use App\Components\Locator;
use App\Models\Enum\VisitPaymentType;
use App\Models\User;
use App\Models\Visit;
use App\Services\Price\PriceService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Visit::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * @param Collection<Visit> $visits
     * @return bool
     */
    public function visitsArePaid(Collection $visits): bool
    {
        foreach ($visits as $visit) {
            if ($visit->payment_type !== \App\Models\Payment::class
                || null === $visit->payment
                || null === $visit->payment->paid_at) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Dto $dto
     * @return Visit
     * @throws \App\Components\Account\Exceptions\InsufficientFundsAccountException|\Exception
     */
    public function createLessonVisit(Dto $dto): Visit
    {
        return \DB::transaction(static function () use ($dto) {
            /** @var PriceService $priceService */
            $priceService = \app(PriceService::class);

            // Create visit
            $visit = Loader::visits()->create($dto);

            // Create payment
            if (null === $dto->promocode_id) {
                $lesson = Loader::lessons()->find($dto->lesson_id);
                $student = Loader::students()->find($dto->student_id);
                $price = $priceService->calculateLessonVisitPrice($lesson, $student);
                $payment = Loader::payments()->createVisitPayment($price, $visit, $student, $user);
                $visit->payment_id = $payment->id;
                $visit->payment_type = VisitPaymentType::PAYMENT;
            } else {
                // $this->promocodeRepository->find($dto->promocode_id);
                // $visit->payment_id = $dto->promocode_id;
                $visit->payment_type = VisitPaymentType::PROMOCODE;
            }
            $visit->save();

            return $visit;
        });
    }

    /**
     * @param Visit $record
     * @param User $user
     * @return void
     * @throws \Throwable
     */
    public function delete(Model $record, \App\Models\User $user): void
    {
        if (null !== $record->payment) {
            throw new Exceptions\CannotDeletePaidVisit();
        }
        parent::delete($record, $user);
    }
}