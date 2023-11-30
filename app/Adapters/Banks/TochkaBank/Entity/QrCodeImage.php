<?php

namespace App\Adapters\Banks\TochkaBank\Entity;

class QrCodeImage implements \App\Adapters\Banks\Contracts\QrCodeImage
{
    public function __construct(
        public readonly int $width,
        public readonly int $height,
        public readonly string $mediaType,
        public readonly string $content,
    ) { }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}