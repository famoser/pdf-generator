<?php

namespace Famoser\PdfGenerator\IR\Document\Content\Common;

readonly class Position
{
    public function __construct(private float $startX, private float $startY)
    {
    }

    public function getStartX(): float
    {
        return $this->startX;
    }

    public function getStartY(): float
    {
        return $this->startY;
    }
}
