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

class Line
{
    /**
     * @var string[]
     */
    private $words;

    /**
     * @var float[]
     */
    private $wordWidths;

    /**
     * @var float
     */
    private $spaceWidth;

    /**
     * @var float
     */
    private $width;

    /**
     * MeasuredPhrase constructor.
     *
     * @param string[] $words
     * @param float[]  $wordWidths
     */
    public function __construct(array $words, array $wordWidths, float $spaceWidth)
    {
        $this->words = $words;
        $this->wordWidths = $wordWidths;
        $this->spaceWidth = $spaceWidth;

        $this->width = array_sum($this->wordWidths) + (\count($this->wordWidths) - 1) * $this->spaceWidth;
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

    public function getWidth(): float
    {
        return $this->width;
    }
}
