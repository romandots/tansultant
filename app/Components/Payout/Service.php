<?php

declare(strict_types=1);

namespace App\Components\Payout;

use App\Common\BaseComponentService;
use App\Common\Contracts;
use App\Components\Loader;
use App\Exceptions\InvalidStatusException;
use App\Jobs\GeneratePayoutReportJob;
use App\Models\Account;
use App\Models\Enum\LessonStatus;
use App\Models\Enum\PayoutStatus;
use App\Models\Formula;
use App\Models\Lesson;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

        \DB::beginTransaction();

        /** @var Payout $payout */
        $payout = parent::create($dto);
        $lessons = Loader::lessons()->getLessonsForPayout(
            $dto->branch_id,
            $dto->instructor_id,
            $dto->period_from,
            $dto->period_to
        );

        $this->attachLessons($payout, $lessons, $dto->getUser());

        \DB::commit();

        return $payout;
    }

    public function createBatch(StoreBatchDto $batchDto): Collection
    {
        $instructorsIds = Loader::instructors()->getInvolvedInstructorsIdsForBranchAndPeriod(
            $batchDto->branch_id,
            $batchDto->period_from,
            $batchDto->period_to
        );
        $payouts = new Collection();
        foreach ($instructorsIds as $instructorId) {
            $dto = new Dto($batchDto->getUser());
            $dto->name = $batchDto->name;
            $dto->period_from = $batchDto->period_from;
            $dto->period_to = $batchDto->period_to;
            $dto->branch_id = $batchDto->branch_id;
            $dto->instructor_id = $instructorId;
            $dto->status = PayoutStatus::CREATED;

            $payout = $this->create($dto);
            $payouts->add($payout);
        }

        return $payouts;
    }

    public function delete(Model $record, \App\Models\User $user): void
    {
        /** @var Payout $record */
        if ($record->status !== PayoutStatus::CREATED) {
            throw new InvalidStatusException($record->status, [PayoutStatus::CREATED]);
        }
        $record->lessons()->detach();
        parent::delete($record, $user);
    }

    public function deleteBatch(\App\Components\Payout\UpdateBatchDto $batch): void
    {
        $payouts = $this->getRepository()->getMany($batch->ids);
        foreach ($payouts as $payout) {
            try {
                $this->delete($payout, $batch->user);
            } catch (\Exception) { }
        }
    }

    private function generatePayoutName(Dto $dto): string
    {
        $substitutions = [
            'period' => $this->getPeriodAsString($dto->period_from, $dto->period_to),
            'branch' => Loader::branches()->findById($dto->branch_id)->name,
            'instructor' => Loader::instructors()->findById($dto->instructor_id)->name,
        ];

        return null === $dto->name
            ? \trans('payout.name', $substitutions)
            : \sprintf($dto->name, $substitutions);
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

    public function transitionBatch(\App\Components\Payout\UpdateBatchDto $batch): void
    {
        $payouts = $this->getRepository()->getMany($batch->ids);
        foreach ($payouts as $payout) {
            $this->setPrepared($payout, $batch->user);
        }
    }

    public function checkoutBatch(\App\Components\Payout\CheckoutBatchDto $batchDto): void
    {
        $payouts = $this->getRepository()->getMany($batchDto->ids);
        $account = Loader::accounts()->findById($batchDto->account_id);
        foreach ($payouts as $payout) {
            $this->checkout($payout, $account, $batchDto->user);
        }
    }

    /**
     * Creates transaction, sets payout status to PAID, sets lessons status to CHECKED_OUT
     * and attaches transaction to payout
     * @param Payout $payout
     * @param Account $account
     * @param User $user
     * @return void
     * @throws \Exception
     */
    protected function checkout(Payout $payout, Account $account, User $user): void
    {
        DB::beginTransaction();
        try {
            $transaction = Loader::transactions()->createPayoutTransaction($payout, $account, $user);
            $this->setPaid($payout, $user);
            $lessons = $payout->load('lessons')->lessons;
            Loader::lessons()->setStatusBatch($lessons, LessonStatus::CHECKED_OUT, $user);
            $this->getRepository()->setTransaction($payout, $transaction);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $this->runGenerateReportBackgroundJob($payout, $user);
    }

    public function setPrepared(Payout $payout, User $user): void
    {
        $this->getRepository()->setPrepared($payout);
        $this->history->logStatus($user, $payout, PayoutStatus::PREPARED->value);
        $this->debug('Payout ' . (string)$payout . ' status is set to PREPARED');
    }

    protected function setPaid(Payout $payout, User $user): void
    {
        $this->getRepository()->setPaid($payout);
        $this->history->logStatus($user, $payout, PayoutStatus::PAID->value);
        $this->debug('Payout ' . (string)$payout . ' status is set to PAID');
    }

    /**
     * @param Payout $payout
     * @param Collection<Lesson> $lessons
     * @param User $user
     * @param Formula|null $formula
     * @return void
     */
    public function attachLessons(Payout $payout, Collection $lessons, User $user, ?Formula $formula = null): void
    {
        foreach ($lessons as $lesson) {
            $formula = $formula ?? $lesson->course->formula ?? null;
            if (null === $formula) {
                continue;
            }
            $this->attachLesson($payout, $lesson, $formula, $user);
        }
    }

    public function detachLessons(Payout $payout, Collection $lessons, User $user): void
    {
        foreach ($lessons as $lesson) {
            $this->detachLesson($payout, $lesson, $user);
        }
    }

    public function updateLessons(\App\Components\Payout\DtoLessons $payoutLessons): void
    {
        $lessons = Loader::lessons()->getMany($payoutLessons->lessons_ids);
        /** @var Payout $payout */
        $payout = Loader::payouts()->findById($payoutLessons->payout_id);
        /** @var Formula $formula */
        $formula = Loader::formulas()->findById($payoutLessons->formula_id);

        $this->attachLessons($payout, $lessons, $payoutLessons->user, $formula);
    }

    public function deleteLessons(\App\Components\Payout\DtoLessons $payoutLessons): void
    {
        /** @var Payout $payout */
        $payout = Loader::payouts()->findById($payoutLessons->payout_id);
        $lessons = Loader::lessons()->getMany($payoutLessons->lessons_ids);
        $this->detachLessons($payout, $lessons, $payoutLessons->user);
    }

    public function attachLesson(Payout $payout, Lesson $lesson, Formula $formula, User $user): void
    {
        $this->getRepository()->attachLessonWithFormula($payout, $lesson, $formula);
        $this->getRepository()->updateTotalAmount($payout);
        $payload = ['formula_id' => $formula->id];
        $this->history->logAttach($user, $payout, $lesson, $payload);
        $this->debug('Lesson ' . (string)$lesson . ' is attached to payment #' . $payout->id, $payload);
    }

    public function detachLesson(Payout $payout, Lesson $lesson, User $user): void
    {
        $this->getRepository()->detachLesson($payout, $lesson);
        $this->getRepository()->updateTotalAmount($payout);
        $this->history->logDetach($user, $payout, $lesson);
        $this->debug('Lesson ' . (string)$lesson . ' is detached from payment #' . $payout->id);
    }

    protected function runGenerateReportBackgroundJob(Payout $payout, User $user): void
    {
        $job = new GeneratePayoutReportJob($payout, $user);
        dispatch($job);
    }

    public function generatePayoutReport(\App\Models\Payout $payout): void
    {
        $stream = Loader::pdfGenerator()->generatePdf('payouts.report', compact('payout'));
        $this->getRepository()->addDocument($payout, $stream,Payout::MEDIA_COLLECTION);
    }
}