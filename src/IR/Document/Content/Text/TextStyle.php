<?php

namespace Famoser\PdfGenerator\IR\Document\Content\Text;

use Famoser\PdfGenerator\IR\Document\Content\Common\Color;
use Famoser\PdfGenerator\IR\Document\Resource\Font;

readonly class TextStyle
{
    public function __construct(private Font $font, private float $fontSize, private float $leading, private float $wordSpace, private Color $color)
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

    public function getWordSpace(): float
    {
        return $this->wordSpace;
    }

    public function getColor(): Color
    {
        return $this->color;
    }
}
