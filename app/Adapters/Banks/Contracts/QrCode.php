<?php

namespace App\Adapters\Banks\Contracts;

use App\Adapters\Banks\TochkaBank\Entity\QrCodeImage;

interface QrCode
{
    public function getSystem(): string;
    public function getLink(): string;
    public function getImage(): QrCodeImage;
}