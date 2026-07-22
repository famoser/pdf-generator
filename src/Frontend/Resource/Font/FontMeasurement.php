<?php

namespace Famoser\PdfGenerator\Frontend\Resource\Font;

use Famoser\PdfGenerator\Frontend\Resource\Font\WordSizer\WordSizerInterface;
use Famoser\PdfGenerator\IR\Document\Resource\Font;

readonly class FontMeasurement
{
    public function __construct(private Font $font, private float $fontSize, private float $lineHeight, private WordSizerInterface $wordSizer)
    {
    }

    public function getAscender(): float
    {
        return $this->font->getAscender() / $this->getFontScaling();
    }

    public function getLineGap(): float
    {
        return $this->getLeading() - $this->getAscender() + $this->getDescender();
    }

    public function getDescender(): float
    {
        return $this->font->getDescender() / $this->getFontScaling();
    }

    public function getLeading(): float
    {
        return $this->font->getBaselineToBaselineDistance() / $this->getFontScaling() * $this->lineHeight;
    }

    public function getFontScaling(): float
    {
        return $this->font->getUnitsPerEm() / $this->fontSize;
    }

    public function getWidth(string $word): float
    {
        return $this->wordSizer->getWidth($word) / $this->getFontScaling();
    }

    public function getSpaceWidth(): float
    {
        return $this->wordSizer->getSpaceWidth() / $this->getFontScaling();
    }

    public function getFontSize(): float
    {
        return $this->fontSize;
    }
}
