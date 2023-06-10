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

use PdfGenerator\Frontend\Block\Style\Part\Color;
use PdfGenerator\Frontend\Content\Style\Base\Style;

class DrawingStyle extends Style
{
    private readonly ?Color $borderColor;

    public function __construct(private readonly float $lineWidth, Color $borderColor = null, private readonly ?Color $fillColor = null)
    {
        $this->borderColor = $borderColor ?? Color::black();
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
