<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Structure\Document\Page\Content\Rectangle;

use PdfGenerator\IR\Structure\Document\Page\Content\Common\Color;

class RectangleStyle
{
    /**
     * Style constructor.
     */
    public function __construct(private readonly float $lineWidth, private readonly ?Color $borderColor, private readonly ?Color $fillColor)
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
