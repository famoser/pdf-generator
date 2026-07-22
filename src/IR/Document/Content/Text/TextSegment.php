<?php

namespace Famoser\PdfGenerator\IR\Document\Content\Text;

readonly class TextSegment
{
    public function __construct(private string $text, private TextStyle $style)
    {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getStyle(): TextStyle
    {
        return $this->style;
    }
}
