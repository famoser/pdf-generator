<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
