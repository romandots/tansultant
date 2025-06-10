<?php
/**
 * File: VerifyService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Verification;

use App\Common\BaseService;
use App\Components\Loader;
use App\Components\VerificationCode\Dto;
use App\Models\VerificationCode;
use App\Services\TextMessaging\TextMessagingServiceInterface;
use Carbon\Carbon;

class VerificationService extends BaseService
{
    protected TextMessagingServiceInterface $messagingService;
    protected \App\Components\VerificationCode\Facade $verificationCodes;
    protected array $config;

    public function __construct(
        TextMessagingServiceInterface $messagingService
    ) {
        $this->messagingService = $messagingService;
        $this->verificationCodes = Loader::verificationCodes();
        $this->config = \app('config')['verification'];
    }

    /**
     * @param string $phoneNumber
     * @param string|null $code
     * @return VerificationCode
     * @throws Exception\VerificationCodeIsInvalid
     */
    public function checkVerificationCode(string $phoneNumber, ?string $code): VerificationCode
    {
        try {
            $verificationCode = $this->verificationCodes->findByPhoneNumberAndVerificationCode($phoneNumber, $code);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            throw new Exception\VerificationCodeIsInvalid();
        }

        if (null !== $verificationCode->expired_at && $verificationCode->expired_at->lessThan(Carbon::now())) {
            throw new Exception\VerificationCodeExpired();
        }

        $this->verificationCodes->updateVerifiedAt($verificationCode);

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
     * @throws Exception\VerificationCodeWasSentTooManyTimes
     * @throws Exception\VerificationCodeAlreadySentRecently
     * @throws Exception\TextMessageSendingFailed
     * @throws \Exception
     */
    public function initNewVerificationCode(string $phoneNumber): void
    {
        if ($this->config['max_tries'] < $this->verificationCodes->countByPhoneNumber($phoneNumber)) {
            throw new Exception\VerificationCodeWasSentTooManyTimes($this->config['max_tries']);
        }

        if ($this->verificationCodes->getByPhoneNumberNotExpired($phoneNumber)) {
            throw new Exception\VerificationCodeAlreadySentRecently($this->config['timeout']);
        }

        $code = $this->generateVerificationCode();

        \DB::transaction(function () use ($phoneNumber, $code) {
            $verificationCodeDto = new Dto();
            $verificationCodeDto->phone_number = $phoneNumber;
            $verificationCodeDto->verification_code = $code;
            $this->verificationCodes->create($verificationCodeDto);
            $this->sendVerificationCode($phoneNumber, $code);
        });
    }

    /**
     * @param string $phoneNumber
     * @param string $verificationCode
     * @throws Exception\TextMessageSendingFailed
     */
    private function sendVerificationCode(string $phoneNumber, string $verificationCode): void
    {
        $message = \trans('verification.verification_text_message', ['code' => $verificationCode]);

        if ($this->config['log_messages']) {
            $this->debug($message, [$phoneNumber, $verificationCode]);
            return;
        }

        if ($this->config['send_messages']) {
            try {
                $this->messagingService->send($phoneNumber, $message);
            } catch (\Exception $exception) {
                throw new Exception\TextMessageSendingFailed($exception->getMessage());
            }
        }
    }

    public function cleanUp(string $phone): void
    {
        $phone = \normalize_phone_number($phone);
        $this->verificationCodes->deleteByPhoneNumber($phone);
    }
}
