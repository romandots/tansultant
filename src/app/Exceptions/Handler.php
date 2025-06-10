<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Components\Account\Exceptions\InsufficientFundsAccountException;
use App\Components\Schedule\Exceptions\ScheduleSlotIsOccupied;
use App\Components\Subscription\Exceptions\Exception as SubscriptionException;
use App\Components\Subscription\Exceptions\ProlongationPeriodExpired;
use App\Components\User\Exceptions\OldPasswordInvalidException;
use App\Components\Visit\Exceptions\NoSubscriptionsException;
use App\Exceptions\Auth\UnauthorizedException;
use App\Services\Login\Exceptions\UserNotFoundException;
use App\Services\Login\Exceptions\WrongPasswordException;
use App\Services\PasswordReset\Exceptions\UserHasNoPerson;
use App\Services\UserRegister\Exceptions\UserAlreadyRegisteredWithOtherPhoneNumber;
use App\Services\UserRegister\Exceptions\UserAlreadyRegisteredWithSamePhoneNumber;
use App\Services\Verification\Exception\TextMessageSendingFailed;
use App\Services\Verification\Exception\VerificationCodeAlreadySentRecently;
use App\Services\Verification\Exception\VerificationCodeExpired;
use App\Services\Verification\Exception\VerificationCodeIsInvalid;
use App\Services\Verification\Exception\VerificationCodeWasSentTooManyTimes;
use Illuminate\Database\Eloquent\RelationNotFoundException;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends BaseExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     * @var array
     */
    protected $dontReport = [
        UnauthorizedException::class,
        UserNotFoundException::class,
        WrongPasswordException::class,
        OldPasswordInvalidException::class,
        UserAlreadyRegisteredWithSamePhoneNumber::class,
        UserAlreadyRegisteredWithOtherPhoneNumber::class,
        VerificationCodeIsInvalid::class,
        VerificationCodeWasSentTooManyTimes::class,
        VerificationCodeAlreadySentRecently::class,
        VerificationCodeExpired::class,
        TextMessageSendingFailed::class,
        UserHasNoPerson::class,
        UserHasNoPersonException::class,
        ScheduleSlotIsOccupied::class,
        NoSubscriptionsException::class,
    ];

    /**
     * Returns a list of user-defined exception handlers
     * Feel free to put your handlers here, and also you can override internal handlers here
     * @return array
     */
    protected function getCustomHandlers(): array
    {
        return [
            AlreadyExistsException::class => [$this, 'renderAsJson'],
            InvalidStatusException::class => [$this, 'renderAsJson'],
            UserAssistanceRequiredException::class => [$this, 'renderAsJson'],
            SimpleValidationException::class => [$this, 'renderAsJson'],
            InsufficientFundsAccountException::class => [$this, 'renderAsJson'],
            ProlongationPeriodExpired::class => [$this, 'renderAsJson'],
            UnauthorizedException::class => [$this, 'renderAsJson'],
            UserNotFoundException::class => [$this, 'renderAsJson'],
            WrongPasswordException::class => [$this, 'renderAsJson'],
            OldPasswordInvalidException::class => [$this, 'renderAsJson'],
            UserAlreadyRegisteredWithSamePhoneNumber::class => [$this, 'renderAsJson'],
            UserAlreadyRegisteredWithOtherPhoneNumber::class => [$this, 'renderAsJson'],
            VerificationCodeIsInvalid::class => [$this, 'renderAsJson'],
            VerificationCodeWasSentTooManyTimes::class => [$this, 'renderAsJson'],
            VerificationCodeAlreadySentRecently::class => [$this, 'renderAsJson'],
            VerificationCodeExpired::class => [$this, 'renderAsJson'],
            TextMessageSendingFailed::class => [$this, 'renderAsJson'],
            UserHasNoPerson::class => [$this, 'renderAsJson'],
            ScheduleSlotIsOccupied::class => [$this, 'renderAsJson'],
            UserHasNoPersonException::class => [$this, 'renderAsJson'],
            BaseException::class => [$this, 'renderAsJson'],
            \BadMethodCallException::class => [$this, 'renderBadMethodCallAsJson'],
            RelationNotFoundException::class => [$this, 'renderRelationNotFoundAsJson'],
            SubscriptionException::class => [$this, 'renderAsJson'],
        ];
    }

    /**
     * @param ReadableExceptionInterface $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function renderAsJson(ReadableExceptionInterface $exception): \Illuminate\Http\JsonResponse
    {
        $payload = $exception->getData();
        try {
            $message = \trans('exceptions.' . $exception->getMessage(), $payload);
        } catch (\Throwable) {
            $message = \trans('exceptions.' . $exception->getMessage());
        }
        $output = [
            'error' => $exception->getMessage(),
            'message' => $message,
        ];
        if (null !== $payload) {
            $output['data'] = $payload;
        }

        return \json_response($output, $exception->getStatusCode());
    }

    /**
     * @param \Throwable $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function renderNativeAsJson(\Throwable $exception): \Illuminate\Http\JsonResponse
    {
        $output = [
            'error' => $exception->getMessage(),
            'message' => \trans('exceptions.' . $exception->getMessage()),
            'data' => $exception->getTrace(),
        ];

        return \json_response($output, 400);
    }

    public function renderBadMethodCallAsJson(\BadMethodCallException $exception): \Illuminate\Http\JsonResponse
    {
        $output = [
            'error' => 'invalid_relation',
            'message' => \trans('exceptions.invalid_relation'),
            'data' => [
                'relation' => $exception->getTrace()[0]['args'][0] ?? null,
                'trace' => $exception->getTrace(),
            ],
        ];

        return \json_response($output, 400);
    }

    public function renderRelationNotFoundAsJson(RelationNotFoundException $exception): \Illuminate\Http\JsonResponse
    {
        $output = [
            'error' => 'invalid_relation',
            'message' => \trans('exceptions.invalid_relation'),
            'data' => [
                'relation' => $exception->relation,
            ],
        ];

        return \json_response($output, 400);
    }
}
