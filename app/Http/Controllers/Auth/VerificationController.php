<?php
declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyPhoneNumberRequest;
use App\Services\Verification\VerificationService;

class VerificationController extends Controller
{
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
     * @param VerificationService $verificationService
     * @param VerifyPhoneNumberRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function verifyPhone(VerificationService $verificationService, VerifyPhoneNumberRequest $request): \Illuminate\Http\JsonResponse
    {
        $dto = $request->getDto();

        if (null === $dto->verification_code) {
            $verificationService->initNewVerificationCode($dto->phone);
            return \json_response(\get_status_message('verification_code_sent'), 201);
        }

        $verificationCode = $verificationService->checkVerificationCode($dto->phone, $dto->verification_code);

        return \json_response(['verification_code_id' => $verificationCode->id]);
    }
}
