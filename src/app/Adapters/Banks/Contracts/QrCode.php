<?php

namespace App\Adapters\Banks\Contracts;

use App\Adapters\Banks\TochkaBank\Entity\QrCodeImage;

interface QrCode
{
    public function getLink(): string;

    public function getImage(): QrCodeImage;

    public function isExpired(): bool;

    public function isPaid(): bool;

    public function isRejected(): bool;

    public function isReceived(): bool;

    public function isInProgress(): bool;
}