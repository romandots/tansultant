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

        $resultData = $this->customRequest('GET', $url);

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

        $accountId = urlencode($this->getAccountId());
        $url = sprintf('%s/qr-code/merchant/%s/%s', $this->getBaseHost(), $merchantId, $accountId);
        $data = [
            'Data' => [
                'amount' => $transaction->amount,
                'paymentPurpose' => $transaction->name,
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

        $resultData = $this->customRequest('POST', $url, $data);

        return new Entity\QrCode($resultData);
    }

    public function getQrCode(?string $id): QrCode
    {
        $url = sprintf('%s/qr-code/%s', $this->getBaseHost(), $id);

        $resultData = $this->customRequest('GET', $url);

        return new Entity\QrCode($resultData);
    }
}