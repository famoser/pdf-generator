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

class MeasuredParagraph
{
    /**
     * @var MeasuredPhrase[]
     */
    private $measuredPhrases = [];

    public function addMeasuredPhrase(MeasuredPhrase $measuredPhrase)
    {
        $this->measuredPhrases[] = $measuredPhrase;
    }

    /**
     * @return MeasuredPhrase[]
     */
    public function getMeasuredPhrases(): array
    {
        return $this->measuredPhrases;
    }

    public function isEmpty(): bool
    {
        return \count($this->measuredPhrases) > 0;
    }

    public function getFirstTextStyle(): TextStyle
    {
        return $this->measuredPhrases[0]->getTextStyle();
    }
}
