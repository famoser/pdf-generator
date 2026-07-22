<?php

namespace Famoser\PdfGenerator\IR\Document\Content\Common;

readonly class Size
{
    public function __construct(private float $width, private float $height)
    {
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }
}
