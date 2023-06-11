<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\FrontendResources\MeasuredContent\Paragraph;

readonly class Line
{
    private readonly float $width;

    /**
     * @param string[] $words
     * @param float[]  $wordWidths
     */
    public function __construct(private readonly array $words, private readonly array $wordWidths, private readonly float $spaceWidth)
    {
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
