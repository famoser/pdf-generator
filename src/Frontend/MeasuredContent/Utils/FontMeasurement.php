<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\MeasuredContent\Utils;

use PdfGenerator\IR\Structure\Document\Font;

class FontMeasurement
{
    private Font $font;

    private float $fontSize;

    private float $lineHeight;

    /**
     * FontMeasurement constructor.
     */
    public function __construct(Font $font, float $fontSize, float $lineHeight)
    {
        $this->font = $font;
        $this->fontSize = $fontSize;
        $this->lineHeight = $lineHeight;
    }

    public function getAscender()
    {
        return $this->font->getAscender() / $this->getFontScaling();
    }

    public function getLineGap()
    {
        return $this->getLeading() - $this->getAscender() + $this->getDescender();
    }

    public function getDescender()
    {
        return $this->font->getDescender() / $this->getFontScaling();
    }

    public function getLeading()
    {
        return $this->font->getBaselineToBaselineDistance() / $this->getFontScaling() * $this->lineHeight;
    }

    public function getFontScaling()
    {
        return $this->font->getUnitsPerEm() / $this->fontSize;
    }
}
