<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\MeasuredContent\Paragraph;

use PdfGenerator\Frontend\Content\Style\TextStyle;
use PdfGenerator\IR\Structure\Document\Font;

class Phrase
{
    /**
     * MeasuredPhrase constructor.
     *
     * @param Line[] $measuredLines
     */
    public function __construct(private array $measuredLines, private TextStyle $textStyle, private Font $font)
    {
    }

    /**
     * @return Line[]
     */
    public function getMeasuredLines(): array
    {
        return $this->measuredLines;
    }

    public function getTextStyle(): TextStyle
    {
        return $this->textStyle;
    }

    public function getFont(): Font
    {
        return $this->font;
    }
}
