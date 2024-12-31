<?php

namespace Famoser\PdfGenerator\Frontend\Layout;

use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;

readonly class TextSpan
{
    public function __construct(private string $text, private TextStyle $textStyle)
    {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getTextStyle(): TextStyle
    {
        return $this->textStyle;
    }

    /**
     * @return string[]
     */
    public function getLines(): array
    {
        $textWithNormalizedNewlines = str_replace(["\r\n", "\n\r", "\r"], "\n", $this->text);

        return explode("\n", $textWithNormalizedNewlines);
    }
}
