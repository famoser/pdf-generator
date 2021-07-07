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

class LineBreaker
{
    /**
     * @var WordSizer
     */
    private $sizer;

    /**
     * @var string[]
     */
    private $words;

    /**
     * the next to be included word.
     *
     * @var int
     */
    private $nextWordIndex = 0;

    public function __construct(WordSizer $sizer, string $text)
    {
        $this->sizer = $sizer;
        $this->words = explode(' ', $text);
    }

    public function hasNextLine(): bool
    {
        return $this->nextWordIndex < \count($this->words);
    }

    public function nextLine(float $targetWidth, bool $allowEmpty): array
    {
        if (!$this->hasNextLine()) {
            return ['', 0];
        }

        $nextWord = $this->words[$this->nextWordIndex++];
        $currentWidth = $this->sizer->getWidth($nextWord);
        $currentWords = $nextWord;

        if ($allowEmpty && $currentWidth > $targetWidth) {
            --$this->nextWordIndex;

            return ['', 0];
        }

        while ($this->nextWordIndex < \count($this->words)) {
            // check if next word fits
            $nextWord = $this->words[$this->nextWordIndex++];
            $nextWidth = $this->sizer->getSpaceWidth() + $this->sizer->getWidth($nextWord);
            if ($currentWidth + $nextWidth > $targetWidth) {
                --$this->nextWordIndex;
                break;
            }

            // add next word
            $currentWords .= ' ' . $nextWord;
            $currentWidth += $nextWidth;
        }

        return [$currentWords, $currentWidth];
    }
}
