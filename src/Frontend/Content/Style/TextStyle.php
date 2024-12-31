<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Content\Style;

use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;

readonly class TextStyle
{
    public function __construct(private Font $font, private float $fontSize = 3.8, private float $leading = 1.2, private Color $color = new Color(0, 0, 0))
    {
    }

    public function getFont(): Font
    {
        return $this->font;
    }

    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    public function getLeading(): float
    {
        return $this->leading;
    }

    public function getColor(): Color
    {
        return $this->color;
    }
}
