<?php

namespace Famoser\PdfGenerator\Frontend\Content\Style;

use Famoser\PdfGenerator\Frontend\Resource\Font;
use Famoser\PdfGenerator\IR\Document\Content\Common\Color;

readonly class TextStyle
{
    public function __construct(private Font $font, private Color $color = new Color(0, 0, 0))
    {
    }

    public function getFont(): Font
    {
        return $this->font;
    }

    public function getColor(): Color
    {
        return $this->color;
    }
}
