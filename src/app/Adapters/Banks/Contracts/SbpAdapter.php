<?php

namespace App\Adapters\Banks\Contracts;

use App\Models\Transaction;

interface SbpAdapter
{
    public function registerQrCode(Transaction $transaction): QrCode;
    public function getQrCode(?string $id): QrCode;
    public function checkQrCodesPaymentStatus(array $qrCodesIds): array;
    public function externalSystemName(): string;
}