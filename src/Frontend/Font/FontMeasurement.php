<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Font;

use PdfGenerator\IR\Document\Resource\Font;

readonly class FontMeasurement
{
    public function __construct(private readonly Font $font, private readonly float $fontSize, private readonly float $lineHeight)
    {
    }

    public function getAscender(): float|int
    {
        return $this->font->getAscender() / $this->getFontScaling();
    }

    public function getLineGap(): float|int
    {
        return $this->getLeading() - $this->getAscender() + $this->getDescender();
    }

    public function getDescender(): float|int
    {
        return $this->font->getDescender() / $this->getFontScaling();
    }

    public function getLeading(): float|int
    {
        return $this->font->getBaselineToBaselineDistance() / $this->getFontScaling() * $this->lineHeight;
    }

    public function getFontScaling(): float|int
    {
        return $this->font->getUnitsPerEm() / $this->fontSize;
    }
}
