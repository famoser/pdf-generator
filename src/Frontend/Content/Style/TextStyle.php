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
use PdfGenerator\Frontend\Content\Style\Part\Font;

class TextStyle
{
    private Font $font;

    private float $fontSize;

    private float $lineHeight;

    private Color $color;

    public function __construct(Font $font, float $fontSize = 12, float $lineHeight = 1.2, Color $color = null)
    {
        $this->font = $font;
        $this->fontSize = $fontSize;
        $this->lineHeight = $lineHeight;
        $this->color = $color ?? Color::black();
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
