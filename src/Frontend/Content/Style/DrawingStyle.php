<?php

namespace Famoser\PdfGenerator\Frontend\Content\Style;

use Famoser\PdfGenerator\Frontend\Layout\Style\ElementStyle;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;

readonly class DrawingStyle
{
    public function __construct(private ?float $lineWidth = 1, private ?Color $lineColor = new Color(0, 0, 0), private ?Color $fillColor = null)
    {
    }

    public static function createFromBlockStyle(ElementStyle $blockStyle): self
    {
        return new self(
            $blockStyle->getBorderWidth() ?? 0,
            $blockStyle->getBorderColor(),
            $blockStyle->getBackgroundColor()
        );
    }

    public function getLineWidth(): ?float
    {
        return $this->lineWidth;
    }

    public function getLineColor(): ?Color
    {
        return $this->lineColor;
    }

    public function getFillColor(): ?Color
    {
        return $this->fillColor;
    }
}
