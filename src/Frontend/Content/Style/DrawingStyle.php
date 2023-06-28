<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Content\Style;

use PdfGenerator\IR\Document\Content\Common\Color;

class DrawingStyle
{
    private ?float $lineWidth;
    private ?Color $lineColor;
    private ?Color $fillColor;

    public function __construct(float $borderWidth = 1, ?Color $borderColor = new Color(0, 0, 0), Color $backgroundColor = null)
    {
        $this->lineWidth = $borderWidth;
        $this->lineColor = $borderColor;
        $this->fillColor = $backgroundColor;
    }

    public function setLineWidth(?float $lineWidth): self
    {
        $this->lineWidth = $lineWidth;

        return $this;
    }

    public function setLineColor(?Color $lineColor): self
    {
        $this->lineColor = $lineColor;

        return $this;
    }

    public function setFillColor(?Color $fillColor): self
    {
        $this->fillColor = $fillColor;

        return $this;
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
