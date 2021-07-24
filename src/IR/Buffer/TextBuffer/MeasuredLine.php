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

class MeasuredLine
{
    /**
     * @var string[]
     */
    private $words = [];

    /**
     * @var float[]
     */
    private $wordWidths;

    /**
     * @var float
     */
    private $spaceWidth;

    /**
     * MeasuredPhrase constructor.
     *
     * @param string[] $words
     * @param float[] $wordWidths
     */
    public function __construct(array $words, array $wordWidths, float $spaceWidth)
    {
        $this->words = $words;
        $this->wordWidths = $wordWidths;
        $this->spaceWidth = $spaceWidth;
    }

    /**
     * @return string[]
     */
    public function getWords(): array
    {
        return $this->words;
    }

    /**
     * @return float[]
     */
    public function getWordWidths(): array
    {
        return $this->wordWidths;
    }

    public function getSpaceWidth(): float
    {
        return $this->spaceWidth;
    }
}
