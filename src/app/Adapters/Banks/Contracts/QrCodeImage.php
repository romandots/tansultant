<?php

namespace App\Adapters\Banks\Contracts;

interface QrCodeImage
{
    public function getWidth(): int;
    public function getHeight(): int;
    public function getMediaType(): string;
    public function getContent(): string;
}