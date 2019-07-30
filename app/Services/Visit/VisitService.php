<?php
/**
 * File: VisitService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-27
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Visit;

use App\Http\Requests\Api\DTO\LessonVisit;
use App\Models\Payment;
use App\Models\User;
use App\Models\Visit;
use App\Repository\LessonRepository;
use App\Repository\StudentRepository;
use App\Repository\VisitRepository;
use App\Services\Payment\PaymentService;
use App\Services\Price\PriceService;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class VisitService
 * @package App\Services\Visit
 */
class VisitService
{
    /**
     * @var VisitRepository
     */
    private $repository;

    /**
     * @var LessonRepository
     */
    private $lessonRepository;

    /**
     * @var StudentRepository
     */
    private $studentRepository;

    /**
     * @var PriceService
     */
    private $priceService;

    /**
     * @var PaymentService
     */
    private $paymentService;

    /**
     * VisitService constructor.
     * @param VisitRepository $repository
     * @param LessonRepository $lessonRepository
     * @param StudentRepository $studentRepository
     * @param PriceService $priceService
     * @param PaymentService $paymentService
     */
    public function __construct(
        VisitRepository $repository,
        LessonRepository $lessonRepository,
        StudentRepository $studentRepository,
        PriceService $priceService,
        PaymentService $paymentService
    ) {
        $this->repository = $repository;
        $this->lessonRepository = $lessonRepository;
        $this->studentRepository = $studentRepository;
        $this->priceService = $priceService;
        $this->paymentService = $paymentService;
    }

    /**
     * @param Collection|Visit[] $visits
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
     * @param LessonVisit $dto
     * @param User|null $user
     * @return Visit
     * @throws \App\Services\Account\Exceptions\InsufficientFundsAccountServiceException
     */
    public function createLessonVisit(LessonVisit $dto, ?User $user = null): Visit
    {
        return \DB::transaction(function () use ($user, $dto) {
            // Create visit
            $visit = $this->repository->createLessonVisitFromDto($dto, $user);

            // Create payment
            if (null === $dto->promocode_id) {
                $lesson = $this->lessonRepository->find($dto->lesson_id);
                $student = $this->studentRepository->find($dto->student_id);
                $price = $this->priceService->calculateLessonVisitPrice($lesson, $student);
                $payment = $this->paymentService->createVisitPayment($price, $visit, $student, $user);
                $visit->payment_id = $payment->id;
                $visit->payment_type = Payment::class;
            } else {
                // $this->promocodeRepository->find($dto->promocode_id);
                $visit->payment_id = $dto->promocode_id;
                $visit->payment_type = 'App\Models\Promocode';
            }
            $visit->save();

            return $visit;
        });
    }
}
