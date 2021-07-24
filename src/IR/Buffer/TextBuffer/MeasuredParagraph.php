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
}
