<?php

namespace App\Adapters\Banks\TochkaBank;

use App\Adapters\Banks\Contracts\QrCode;
use App\Adapters\Banks\Contracts\SbpAdapter;
use App\Adapters\Banks\TochkaBank\Entity\Payment;
use App\Models\Transaction;

class TochkaBankSbpAdapterMock implements SbpAdapter
{

    /**
     * @return Entity\Payment[]
     */
    public function checkQrCodesPaymentStatus(array $qrCodesIds): array
    {
        return [
            new Payment([
                "qrcId" => "AS000000000000000000000000000001",
                "code" => "RQ00000",
                "status" => "InProgress",
                "message" => "Запрос обработан успешно",
                "trxId" => "X1A2S3D5F6G7H8J9K0C4S5C6D7V5D1K2"
            ])
        ];
    }

    public function registerQrCode(Transaction $transaction): QrCode
    {
        return new Entity\QrCode([
            "qrcId" => "AS000000000000000000000000000001",
            "payload" => "https =>//qr.nspk.ru/AS1000670LSS7DN18SJQDNP4B05KLJL2?type=01&bank=100000000001&sum=10000&cur=RUB&crc=C08B",
            "image" => [
                "width" => 0,
                "height" => 0,
                "mediaType" => "image/png",
                "content" => "iVBORw0KGgoAAAANSUhEUgAAASwAAAEs..."
            ],
        ]);
    }

    public function getQrCode(?string $id): QrCode
    {
        return new Entity\QrCode([
            "accountId" => "40817810802000000008/044525104",
            "status" => "Active",
            "createdAt" => "2019-01-01T06 =>06 =>06.364+00 =>00",
            "qrcId" => "AS000000000000000000000000000001",
            "legalId" => "LF0000000001",
            "merchantId" => "MF0000000001",
            "amount" => 0,
            "commissionPercent" => 0,
            "currency" => "RUB",
            "paymentPurpose" => "?",
            "qrcType" => "01",
            "templateVersion" => "01",
            "payload" => "\"https =>//qr.nspk.ru/AS1000670LSS7DN18SJQDNP4B05KLJL2?type=01&bank=100000000001&sum=10000&cur=RUB&crc=C08B\"",
            "sourceName" => "tochka.com",
            "ttl" => "60",
            "image" => [
                "width" => 0,
                "height" => 0,
                "mediaType" => "image/png",
                "content" => "iVBORw0KGgoAAAANSUhEUgAAASwAAAEs..."
            ],
        ]);
    }

    public function externalSystemName(): string
    {
        return 'tochkabank';
    }
}