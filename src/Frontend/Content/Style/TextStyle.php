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

use PdfGenerator\Frontend\Resource\Font;
use PdfGenerator\IR\Document\Content\Common\Color;

class TextStyle
{
    private Color $color;

    public function __construct(private Font $font, private float $fontSize = 3.8, private float $lineHeight = 1.2, Color $color = null)
    {
        $this->color = $color ?? new Color(0, 0, 0);
    }

    public function setFont(Font $font): self
    {
        $this->font = $font;

        return $this;
    }

    public function setFontSize(float $fontSize): self
    {
        $this->fontSize = $fontSize;

        return $this;
    }

    public function setLineHeight(float $lineHeight): self
    {
        $this->lineHeight = $lineHeight;

        return $this;
    }

    public function setColor(Color $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getFont(): Font
    {
        return $this->font;
    }

    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    public function getLineHeight(): float
    {
        return $this->lineHeight;
    }

    public function getColor(): Color
    {
        return $this->color;
    }
}
