<?php

namespace Famoser\PdfGenerator\Frontend\Content\Text;

use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontMeasurement;

readonly class TextSegment
{
    public function __construct(private string $text, private TextStyle $textStyle, private FontMeasurement $fontMeasurement)
    {
    }

    public function cloneWithText(string $text): self
    {
        return new self($text, $this->textStyle, $this->fontMeasurement);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getTextStyle(): TextStyle
    {
        return $this->textStyle;
    }

    public function getFontMeasurement(): FontMeasurement
    {
        return $this->fontMeasurement;
    }
}
