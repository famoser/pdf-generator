<?php

namespace Famoser\PdfGenerator\Frontend\Layout;

use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;

readonly class TextSpan
{
    public function __construct(private string $text, private TextStyle $textStyle, private float $fontSize, private float $lineHeight)
    {
    }

    public function cloneWithText(string $text): self
    {
        return new self($text, $this->textStyle, $this->fontSize, $this->lineHeight);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getTextStyle(): TextStyle
    {
        return $this->textStyle;
    }

    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    public function getLineHeight(): float
    {
        return $this->lineHeight;
    }
}
