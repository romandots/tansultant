<?php

namespace App\Adapters\Banks\TochkaBank;

use App\Adapters\Banks\Contracts\QrCode;
use App\Adapters\Banks\Contracts\SbpAdapter;
use App\Adapters\Banks\TochkaBank\Enum\QrType;
use App\Models\Transaction;

class TochkaBankSbpAdapter extends TochkaBankClient implements SbpAdapter
{

    /**
     * @return Entity\Payment[]
     */
    public function checkQrCodesPaymentStatus(array $qrCodesIds): array
    {
        $ids = implode(',', $qrCodesIds);
        $url = sprintf('%s/qr-codes/%s/payment-status', $this->getBaseHost(), $ids);

        $resultData = $this->customRequest(
            fn () => $this->api->custom()->request('GET', $url)
        );

        return array_map(
            static fn (array $datum) => new Entity\Payment($datum),
            $resultData['paymentList']
        );
    }

    public function registerQrCode(Transaction $transaction): QrCode
    {
        if ($transaction->account->external_system !== $this->externalSystemName()) {
            throw new Exceptions\TochkaBankAdapterException('Transaction external system is not ' . $this->externalSystemName());
        }

        $merchantId = $transaction->account->external_id;
        if (null === $merchantId) {
            throw new Exceptions\TochkaBankAdapterException('Merchant id is not set for account #' . $transaction->account?->id);
        }

        $accountId = $this->getAccountId();
        $url = sprintf('%s/qr-code/merchant/%s/%s', $this->getBaseHost(), $merchantId, $accountId);
        $data = [
            'Data' => [
                'amount' => $transaction->amount,
                'paymentPurpose' => $transaction->comment,
                'currency' => self::CURRENCY,
                'qrcType' => QrType::DYNAMIC->value,
                'imageParams' => [
                    'width' => $this->getConfig('qr.width'),
                    'height' => $this->getConfig('qr.height'),
                    'mediaType' => $this->getConfig('qr.media_type'),
                ],
                'sourceName' => config('app.name'),
                'ttl' => $this->getConfig('qr.ttl'),
                'redirectUrl' => $this->getConfig('qr.redirect_url'),
            ],
        ];

        $resultData = $this->customRequest(
            fn () => $this->api->custom()->request('POST', $url, $data)
        );

        return new Entity\QrCode($resultData);
    }

    public function getQrCode(?string $id): QrCode
    {
        $url = sprintf('%s/qr-code/%s', $this->getBaseHost(), $id);

        $resultData = $this->customRequest(
            fn () => $this->api->custom()->request('GET', $url)
        );

        return new Entity\QrCode($resultData);
    }

    protected function customRequest(callable $request): array
    {
        try {
            $result = $request();
        } catch (\Exception $e) {
            try {
                $jsonMessage = json_decode($e->getMessage(), true, 512, JSON_THROW_ON_ERROR);
                throw new Exceptions\TochkaBankAdapterException(
                    is_array($jsonMessage) ? $jsonMessage['message'] : $e->getMessage(),
                    is_array($jsonMessage) ? $jsonMessage : [],
                    $e->getCode()
                );
            } catch (\JsonException $jsonException) {
                throw new Exceptions\TochkaBankAdapterException($e->getMessage(), [], $e->getCode());
            }
        }

        if ($result === null) {
            throw new Exceptions\TochkaBankAdapterException('Empty response');
        }

        if (!array_key_exists('Data', $result)) {
            throw new Exceptions\TochkaBankAdapterException('Invalid response');
        }

        return $result['Data'];
    }
}