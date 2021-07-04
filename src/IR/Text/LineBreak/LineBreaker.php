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

class LineBreaker
{
    /**
     * @var FontSizer
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
    private $nextWordIndex;

    public function __construct(FontSizer $sizer, string $text)
    {
        $this->sizer = $sizer;
        $this->words = explode(' ', $text);
    }

    public function hasNextLine(): bool
    {
        return $this->nextWordIndex < $this->words;
    }

    public function nextLine(float $width): array
    {
        if (!$this->hasNextLine()) {
            throw new \Exception('No next line');
        }

        $nextWord = $this->words[$this->nextWordIndex];
        $currentWidth = $this->sizer->getWidth($nextWord);
        $currentWords = $nextWord;

        while (true) {
            // check if next word exists
            if (++$this->nextWordIndex >= \count($this->words)) {
                break;
            }

            // check if next word fits
            $nextWord = $this->words[$this->nextWordIndex];
            $nextWidth = $this->sizer->getSpaceWidth() + $this->sizer->getWidth($nextWord);
            if ($currentWidth + $nextWidth > $width) {
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
