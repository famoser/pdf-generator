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
    private Color $color;

    public function __construct(private Font $font, private float $fontSize = 12, private float $lineHeight = 1.2, Color $color = null)
    {
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
