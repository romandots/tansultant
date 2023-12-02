<?php

namespace App\Adapters\Banks\TochkaBank\Entity;

use App\Adapters\Banks\TochkaBank\Enum\QrStatus;
use App\Adapters\Banks\TochkaBank\Enum\QrType;
use App\Adapters\Banks\TochkaBank\Exceptions\TochkaBankAdapterException;
use Carbon\Carbon;

class QrCode implements \App\Adapters\Banks\Contracts\QrCode
{

    public readonly string $id;
    public readonly string $payload;
    public readonly QrCodeImage $image;
    public readonly ?string $accountId;
    public readonly ?QrStatus $status;
    public readonly ?Carbon $createdAt;
    public readonly ?string $legalId;
    public readonly ?string $merchantId;
    public readonly ?int $amount;
    public readonly ?float $commissionPercent;
    public readonly ?string $currency;
    public readonly ?string $paymentPurpose;
    public readonly ?QrType $qrcType;
    public readonly ?string $templateVersion;
    public readonly ?string $sourceName;
    public readonly ?string $ttl;

    public function __construct(array $data)
    {
        try {
            $this->id = $data['qrcId'] ?? null;
            $this->payload = $data['payload'] ?? null;
            $this->image = new QrCodeImage(
                width: $data['image']['width'] ?? 0,
                height: $data['image']['height'] ?? 0,
                mediaType: $data['image']['mediaType'] ?? '',
                content: $data['image']['content'] ?? ''
            );
            $this->accountId = $data['accountId'] ?? null;
            $this->status = isset($data['status']) ? QrStatus::tryFrom($data['status']) : null;
            $this->createdAt = isset($data['createdAt']) ? Carbon::parse($data['createdAt']) : null;
            $this->legalId = $data['legalId'] ?? null;
            $this->merchantId = $data['merchantId'] ?? null;
            $this->amount = isset($data['amount']) ? (int)$data['amount'] : null;
            $this->commissionPercent = isset($data['commissionPercent']) ? (float)$data['commissionPercent'] : null;
            $this->currency = $data['currency'] ?? null;
            $this->paymentPurpose = $data['paymentPurpose'] ?? null;
            $this->qrcType = isset($data['qrcType']) ? QrType::tryFrom($data['qrcType']) : null;
            $this->templateVersion = $data['templateVersion'] ?? null;
            $this->sourceName = $data['sourceName'] ?? null;
            $this->ttl = $data['ttl'] ?? null;
        } catch (\Throwable $throwable) {
            throw new TochkaBankAdapterException('Invalid QR code response: ' . $throwable->getMessage());
        }
    }

    public function getSystem(): string
    {
        return 'tochkabank';
    }

    public function getLink(): string
    {
        return $this->payload;
    }

    public function getImage(): QrCodeImage
    {
        return $this->image;
    }

    public function isPaid(): bool
    {
        return QrStatus::ACCEPTED === $this->status;
    }

    public function isRejected(): bool
    {
        return QrStatus::REJECTED === $this->status;
    }

    public function isReceived(): bool
    {
        return QrStatus::RECEIVED === $this->status;
    }

    public function isInProgress(): bool
    {
        return QrStatus::IN_PROGRESS === $this->status;
    }

    public function isExpired(): bool
    {
        return $this->createdAt->addMinutes($this->ttl)->isPast();
    }
}