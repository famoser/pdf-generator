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
     * @var Line[]
     */
    private $measuredLines;

    /**
     * @var TextStyle
     */
    private $textStyle;

    /**
     * @var Font
     */
    private $font;

    /**
     * MeasuredPhrase constructor.
     *
     * @param Line[] $measuredLines
     */
    public function __construct(array $measuredLines, TextStyle $textStyle, Font $font)
    {
        $this->measuredLines = $measuredLines;
        $this->textStyle = $textStyle;
        $this->font = $font;
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
