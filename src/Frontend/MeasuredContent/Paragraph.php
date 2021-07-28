<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\MeasuredContent;

use PdfGenerator\Frontend\MeasuredContent\Base\MeasuredContent;
use PdfGenerator\Frontend\MeasuredContent\Paragraph\Phrase;

class Paragraph extends MeasuredContent
{
    /**
     * @var Phrase[]
     */
    private $phrases = [];

    public function addPhrase(Phrase $phrase)
    {
        $this->phrases[] = $phrase;
    }

    /**
     * @return Phrase[]
     */
    public function getPhrases(): array
    {
        return $this->phrases;
    }

    public function getWidth(): float
    {
        $maxWidth = 0;
        foreach ($this->phrases as $measuredPhrase) {
            foreach ($measuredPhrase->getMeasuredLines() as $measuredLine) {
                $maxWidth = max($maxWidth, $measuredLine->getWidth());
            }
        }

        return $maxWidth;
    }
}
