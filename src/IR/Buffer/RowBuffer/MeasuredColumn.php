<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Buffer\RowBuffer;

use PdfGenerator\IR\Buffer\TextBuffer\MeasuredParagraph;

class MeasuredColumn
{
    /**
     * @var MeasuredParagraph[]
     */
    private array $measuredParagraphs = [];

    public function addMeasuredParagraph(MeasuredParagraph $measuredParagraph)
    {
        $this->measuredParagraphs[] = $measuredParagraph;
    }

    /**
     * @return MeasuredParagraph[]
     */
    public function getMeasuredParagraphs(): array
    {
        return $this->measuredParagraphs;
    }
}
