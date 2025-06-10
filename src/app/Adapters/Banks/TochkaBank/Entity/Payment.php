<?php

namespace App\Adapters\Banks\TochkaBank\Entity;

use App\Adapters\Banks\TochkaBank\Enum\QrStatus;
use App\Adapters\Banks\TochkaBank\Exceptions\TochkaBankAdapterException;

class Payment
{
    public readonly string $id;
    public readonly string $code;
    public readonly QrStatus $status;
    public readonly string $message;
    public readonly string $trxId;

    public function __construct(array $data)
    {
        try {
            $this->id = $data['qrcId'] ?? null;
            $this->code = $data['code'] ?? null;
            $this->status = QrStatus::tryFrom($data['status'] ?? null);
            $this->message = $data['message'] ?? null;
            $this->trxId = $data['trxId'] ?? null;
        } catch (\Throwable $throwable) {
            throw new TochkaBankAdapterException('Invalid QR code response from bank: ' . $throwable->getMessage());
        }
    }
}