<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    public function getFontSize(): float
    {
        return $this->fontSize;
    }
}
