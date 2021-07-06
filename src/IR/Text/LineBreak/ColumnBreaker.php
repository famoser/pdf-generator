<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Text\LineBreak;

use PdfGenerator\IR\Text\LineBreak\WordSizer\WordSizer;

class ColumnBreaker
{
    /**
     * @var LineBreaker
     */
    private $lineBreaker;

    /**
     * ColumnBreaker constructor.
     */
    public function __construct(WordSizer $sizer, string $text)
    {
        $this->lineBreaker = new LineBreaker($sizer, $text);
    }

    public function hasMoreLines(): bool
    {
        return $this->lineBreaker->hasNextLine();
    }

    public function nextLine(float $targetWidth)
    {
        return $this->lineBreaker->nextLine($targetWidth);
    }

    public function nextColumn(float $targetWidth, int $maxLines)
    {
        $lines = [];
        $lineWidths = [];
        do {
            [$words, $width] = $this->lineBreaker->nextLine($targetWidth);
            $lines[] = $words;
            $lineWidths[] = $width;
        } while ($this->lineBreaker->hasNextLine() && \count($lines) < $maxLines);

        return [$lines, $lineWidths];
    }
}
