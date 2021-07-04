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

use PdfGenerator\IR\Text\LineBreak\FontSizer\FontSizer;

class ColumnBreaker
{
    /**
     * @var LineBreaker
     */
    private $lineBreaker;

    /**
     * ColumnBreaker constructor.
     */
    public function __construct(FontSizer $sizer, string $text)
    {
        $this->lineBreaker = new LineBreaker($sizer, $text);
    }

    public function hasNextColumn(): bool
    {
        return $this->lineBreaker->hasNextLine();
    }

    public function nextColumn(float $width, int $maxLines)
    {
        $lines = [];
        $lineWidths = 0;
        do {
            [$words, $width] = $this->lineBreaker->nextLine($width);
            $lines[] = $words;
            $lineWidths[] = $width;
        } while ($this->lineBreaker->hasNextLine() && \count($lines) < $maxLines);

        return [$lines, $lineWidths];
    }
}
