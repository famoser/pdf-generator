<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Style;

use PdfGenerator\Frontend\Style\Part\Color;
use PdfGenerator\Frontend\Style\Part\Font;

class TextStyle
{
    /**
     * @var Font
     */
    private $font;

    /**
     * @var float
     */
    private $fontSize;

    /**
     * @var Color
     */
    private $color;

    public function __construct(Font $font, float $fontSize = 12, Color $color = null)
    {
        $this->font = $font;
        $this->fontSize = $fontSize;
        $this->color = $color ?? Color::black();
    }

    public function getFont(): Font
    {
        return $this->font;
    }

    /**
     * @return float
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }

    public function getColor(): Color
    {
        return $this->color;
    }
}
