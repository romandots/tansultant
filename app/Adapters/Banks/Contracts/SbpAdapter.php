<?php

namespace App\Adapters\Banks\Contracts;

interface SbpAdapter
{
    public function registerQrCode(int $amount, string $comment): QrCode;
    public function getQrCode(?string $id): QrCode;
    public function checkQrCodesPaymentStatus(array $qrCodesIds): array;
}