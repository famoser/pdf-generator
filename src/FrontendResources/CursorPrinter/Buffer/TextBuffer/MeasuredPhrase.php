<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\FrontendResources\CursorPrinter\Buffer\TextBuffer;

use PdfGenerator\IR\Document\Content\Text\TextStyle;

class MeasuredPhrase
{
    /**
     * @param MeasuredLine[] $measuredLines
     */
    public function __construct(private readonly array $measuredLines, private readonly TextStyle $textStyle)
    {
    }

    /**
     * @return MeasuredLine[]
     */
    public function getMeasuredLines(): array
    {
        return $this->measuredLines;
    }

    public function getTextStyle(): TextStyle
    {
        return $this->textStyle;
    }
}
