<?php

namespace Famoser\PdfGenerator\IR\Document\Content\Rectangle;

use Famoser\PdfGenerator\IR\Document\Content\Common\Color;

readonly class RectangleStyle
{
    public function __construct(private float $lineWidth, private ?Color $borderColor, private ?Color $fillColor)
    {
    }

    public function getLineWidth(): float
    {
        return $this->lineWidth;
    }

    public function getBorderColor(): ?Color
    {
        return $this->borderColor;
    }

    public function getFillColor(): ?Color
    {
        return $this->fillColor;
    }
}
