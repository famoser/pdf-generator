<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Allocator\Content\ParagraphAllocator;

use PdfGenerator\Frontend\MeasuredContent\Paragraph\Line;

class LineBreaker
{
    private Line $line;

    /**
     * the next to be included word.
     */
    private int $nextWordIndex = 0;

    public function __construct(Line $line)
    {
        $this->line = $line;
    }

    public function isEmpty(): bool
    {
        return $this->nextWordIndex >= \count($this->line->getWords());
    }

    public function nextLine(float $targetWidth, bool $allowEmpty): array
    {
        \assert(!$this->isEmpty());

        $nextWordAndWidth = function () {
            $nextWord = $this->line->getWords()[$this->nextWordIndex];
            $nextWidth = $this->line->getWordWidths()[$this->nextWordIndex];
            ++$this->nextWordIndex;

            return [$nextWord, $nextWidth];
        };

        [$nextWord, $nextWidth] = $nextWordAndWidth();

        // early-out for long words
        if ($nextWidth > $targetWidth) {
            if ($allowEmpty) {
                --$this->nextWordIndex;

                return ['', 0];
            }

            return [$nextWord, $nextWidth];
        }

        // keep adding words until line is full
        $currentWords = $nextWord;
        $currentWidth = $nextWidth;
        while ($this->nextWordIndex < \count($this->line->getWords())) {
            // check if next word fits
            [$nextWord, $nextWidth] = $nextWordAndWidth();
            $widthWithSpace = $this->line->getSpaceWidth() + $nextWidth;
            if ($currentWidth + $widthWithSpace > $targetWidth) {
                --$this->nextWordIndex;
                break;
            }

            // add next word
            $currentWords .= ' '.$nextWord;
            $currentWidth += $widthWithSpace;
        }

        return [$currentWords, $currentWidth];
    }
}
