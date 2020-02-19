<?php
declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyPhoneNumberRequest;
use App\Services\UserRegister\UserRegisterService;
use App\Services\Verify\Exceptions\TextMessageSendingFailed;
use App\Services\Verify\Exceptions\VerificationCodeAlreadySentRecently;
use App\Services\Verify\Exceptions\VerificationCodeIsInvalid;
use App\Services\Verify\Exceptions\VerificationCodeWasSentTooManyTimes;
use App\Services\Verify\VerificationService;
use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    use VerifiesEmails;

    private VerificationService $verificationService;

    public function __construct(VerificationService $verificationService) {
        $this->verificationService = $verificationService;
    }

    /**
     * Верификация номера телефона через СМС
     *
     * Если на входе метод получает только номер телефона, то для
     * него будет сгенерирован и отправлен по СМС новый код подтверждения.
     *
     * Если методу также передать код подтверждения, будет предпринята попытка
     * сравнения его значения со сгенерированным кодом. В случае успеха вернется
     * ID кода подтверждения, который в дальнейшем надо использовать для регистрации
     * или восстановления пароля.
     *
     * @param VerifyPhoneNumberRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws VerificationCodeWasSentTooManyTimes
     * @throws VerificationCodeAlreadySentRecently
     * @throws TextMessageSendingFailed
     * @throws VerificationCodeIsInvalid
     * @throws \Exception
     */
    public function verifyPhone(VerifyPhoneNumberRequest $request): \Illuminate\Http\JsonResponse
    {
        $dto = $request->getDto();

        if (null === $dto->verification_code) {
            $this->verificationService->initNewVerificationCode($dto->phone);
            return \json_response(\get_status_message('verification_code_sent'), 201);
        }

        $verificationCode = $this->verificationService->checkVerificationCode($dto->phone, $dto->verification_code);

        return \json_response(['verification_code_id' => $verificationCode->id]);
    }
}
