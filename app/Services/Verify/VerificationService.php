<?php
/**
 * File: VerifyService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Verify;

use App\Models\VerificationCode;
use App\Repository\VerificationCodeRepository;
use App\Services\TextMessaging\TextMessagingServiceInterface;
use Carbon\Carbon;

/**
 * Class VerifyService
 * @package App\Services\Verify
 */
class VerificationService
{
    private VerificationCodeRepository $codeRepository;

    private TextMessagingServiceInterface $messagingService;

    private array $config;

    /**
     * VerifyService constructor.
     * @param VerificationCodeRepository $codeRepository
     * @param TextMessagingServiceInterface $messagingService
     */
    public function __construct(
        VerificationCodeRepository $codeRepository,
        TextMessagingServiceInterface $messagingService
    ) {
        $this->codeRepository = $codeRepository;
        $this->config = \app('config')['verification'];
        $this->messagingService = $messagingService;
    }

    /**
     * @param string $phoneNumber
     * @param string|null $code
     * @return VerificationCode
     * @throws Exceptions\VerificationCodeIsInvalid
     */
    public function checkVerificationCode(string $phoneNumber, ?string $code): VerificationCode
    {
        try {
            $verificationCode = $this->codeRepository->findByPhoneNumberAndVerificationCode($phoneNumber, $code);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            throw new Exceptions\VerificationCodeIsInvalid();
        }

        if (null !== $verificationCode->expired_at && $verificationCode->expired_at->lessThan(Carbon::now())) {
            throw new Exceptions\VerificationCodeExpired();
        }

        $this->codeRepository->updateVerifiedAt($verificationCode);

        return $verificationCode;
    }

    private function generateVerificationCode(): string
    {
        $chars = [];

        if ($this->config['use_letters']) {
            $chars = \range('A', 'Z');
        }

        if ($this->config['use_digits'] || [] === $chars) {
            $chars = \array_merge(\range(0, 9), $chars);
        }

        $charsLength = \count($chars) - 1;

        $code = '';
        for ($i = 0; $i < $this->config['code_length']; $i++) {
            try {
                $randomIndex = (int)\random_int(0, $charsLength);
            } catch (\Exception $exception) {
                $randomIndex = $charsLength;
            }
            $code .= $chars[$randomIndex];
        }

        return $code;
    }

    /**
     * @param string $phoneNumber
     * @throws Exceptions\VerificationCodeWasSentTooManyTimes
     * @throws Exceptions\VerificationCodeAlreadySentRecently
     * @throws Exceptions\TextMessageSendingFailed
     * @throws \Exception
     */
    public function initNewVerificationCode(string $phoneNumber): void
    {
        if ($this->config['max_tries'] < $this->codeRepository->countByPhoneNumber($phoneNumber)) {
            throw new Exceptions\VerificationCodeWasSentTooManyTimes($this->config['max_tries']);
        }

        if ($this->codeRepository->getByPhoneNumberNotExpired($phoneNumber)) {
            throw new Exceptions\VerificationCodeAlreadySentRecently($this->config['timeout']);
        }

        $code = $this->generateVerificationCode();

        \DB::transaction(function () use ($phoneNumber, $code) {
            $this->codeRepository->create($phoneNumber, $code);
            $this->sendVerificationCode($phoneNumber, $code);
        });
    }

    /**
     * @param string $phoneNumber
     * @param string $verificationCode
     * @throws Exceptions\TextMessageSendingFailed
     */
    private function sendVerificationCode(string $phoneNumber, string $verificationCode): void
    {
        $message = \trans('verification.verification_text_message', ['code' => $verificationCode]);

        if ($this->config['log_messages']) {
            \Log::debug($message, [$phoneNumber, $verificationCode]);
            return;
        }

        if ($this->config['send_messages']) {
            try {
                $this->messagingService->send($phoneNumber, $message);
            } catch (\Exception $exception) {
                throw new Exceptions\TextMessageSendingFailed($exception->getMessage());
            }
        }
    }

    public function cleanUp(string $phone): void
    {
        $phone = \normalize_phone_number($phone);
        $this->codeRepository->removeRecordsByPhone($phone);
    }
}
