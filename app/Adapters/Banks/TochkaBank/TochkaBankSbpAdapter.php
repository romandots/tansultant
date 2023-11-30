<?php

namespace App\Adapters\Banks\TochkaBank;

use App\Adapters\Banks\Contracts\QrCode;
use App\Adapters\Banks\Contracts\SbpAdapter;
use App\Adapters\Banks\TochkaBank\Enum\QrType;

class TochkaBankSbpAdapter extends TochkaBankClient implements SbpAdapter
{

    /**
     * @return Entity\Payment[]
     */
    public function checkQrCodesPaymentStatus(array $qrCodesIds): array
    {
        $baseHost = $this->getBaseHost();
        $ids = implode(',', $qrCodesIds);
        $url = sprintf('%s/qr-codes/%s/payment-status', $baseHost, $ids);

        $result = $this->api->custom()->request('GET', $url);

        if (!array_key_exists('Data', $result) || !array_key_exists('paymentList', $result['Data'])) {
            throw new Exceptions\TochkaBankAdapterException('Invalid response');
        }

        return array_map(
            static fn (array $datum) => new Entity\Payment($datum),
            $result['Data']['paymentList']
        );
    }

    public function registerQrCode(int $amount, string $comment): QrCode
    {
        $baseHost = $this->getBaseHost();
        $merchantId = $this->getMerchantId();
        $accountId = $this->getAccountId();
        $url = sprintf('%s/qr-code/merchant/%s/%s', $baseHost, $merchantId, $accountId);
        $data = [
            'Data' => [
                'amount' => $amount,
                'paymentPurpose' => $comment,
                'currency' => self::CURRENCY,
                'qrcType' => QrType::DYNAMIC,
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

        $result = $this->api->custom()->request('POST', $url, $data);

        if (!array_key_exists('Data', $result)) {
            throw new Exceptions\TochkaBankAdapterException('Invalid response');
        }

        return new Entity\QrCode($result['Data']);
    }

    public function getQrCode(?string $id): QrCode
    {
        $baseHost = $this->getBaseHost();
        $url = sprintf('%s/qr-code/%s', $baseHost, $id);

        $result = $this->api->custom()->request('GET', $url, );

        if (!array_key_exists('Data', $result)) {
            throw new Exceptions\TochkaBankAdapterException('Invalid response');
        }

        return new Entity\QrCode($result['Data']);
    }
}