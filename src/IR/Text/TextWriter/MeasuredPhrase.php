<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Text\TextWriter;

use PdfGenerator\IR\Structure\Document\Page\Content\Text\TextStyle;

class MeasuredPhrase extends Phrase
{
    /**
     * @var float
     */
    private $indent = 0;

    /**
     * @var float[]
     */
    private $lineWidths;

    /**
     * @return float
     */
    public function getIndent()
    {
        return $this->indent;
    }

    /**
     * @param float $indent
     */
    public function setIndent($indent): void
    {
        $this->indent = $indent;
    }

    /**
     * @return float[]
     */
    public function getLineWidths(): array
    {
        return $this->lineWidths;
    }

    /**
     * @param float[] $lineWidths
     */
    public function setLineWidths(array $lineWidths): void
    {
        $this->lineWidths = $lineWidths;
    }

    public static function create(TextStyle $textStyle, array $lines, array $lineWidths, float $indent)
    {
        $self = new self();

        $self->setTextStyle($textStyle);
        $self->setText(implode("\n", $lines));
        $self->setLineWidths($lineWidths);
        $self->setIndent($indent);

        return $self;
    }
}
