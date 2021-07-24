<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Text\TextBuffer;

use PdfGenerator\IR\Structure\Document\Page\Content\Text\TextStyle;

class MeasuredPhrase
{
    /**
     * @var TextStyle
     */
    private $textStyle;

    /**
     * @var string[]
     */
    private $lines;

    /**
     * @var float[]
     */
    private $lineWidths;

    public function getTextStyle(): TextStyle
    {
        return $this->textStyle;
    }

    /**
     * @return string[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * @return float[]
     */
    public function getLineWidths(): array
    {
        return $this->lineWidths;
    }

    public static function create(TextStyle $textStyle, array $lines, array $lineWidths)
    {
        $self = new self();

        $self->textStyle = $textStyle;
        $self->lines = $lines;
        $self->lineWidths = $lineWidths;

        return $self;
    }
}
