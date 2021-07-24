<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Buffer\TextBuffer;

use PdfGenerator\IR\Structure\Document\Page\Content\Text\TextStyle;

class MeasuredPhrase
{
    /**
     * @var MeasuredLine[]
     */
    private $measuredLines;

    /**
     * @var TextStyle
     */
    private $textStyle;

    /**
     * MeasuredPhrase constructor.
     *
     * @param MeasuredLine[] $measuredLines
     */
    public function __construct(array $measuredLines, TextStyle $textStyle)
    {
        $this->measuredLines = $measuredLines;
        $this->textStyle = $textStyle;
    }
}
