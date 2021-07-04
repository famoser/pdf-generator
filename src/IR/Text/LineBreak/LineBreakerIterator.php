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

class LineBreakerIterator implements \Iterator
{
    /**
     * @var FontSizer
     */
    private $sizer;

    /**
     * @var float
     */
    private $width;

    /**
     * @var string[]
     */
    private $words;

    /**
     * @var int
     */
    private $startWordPosition;

    /**
     * the next to be included word.
     *
     * @var int
     */
    private $currentWordPosition;

    /**
     * the chosen words of the current line.
     *
     * @var string
     */
    private $currentWords;

    /**
     * the line width of the @see $currentWords.
     *
     * @var float
     */
    private $currentWidth;

    /**
     * LineBreakerIterator constructor.
     *
     * @param string[] $words
     */
    public function __construct(FontSizer $sizer, float $width, array $words, int $wordPosition)
    {
        $this->sizer = $sizer;
        $this->width = $width;
        $this->words = $words;
        $this->startWordPosition = $wordPosition;

        $this->currentWordPosition = $wordPosition;
        $this->rewind();
    }

    public function current()
    {
        return [$this->currentWords, $this->currentWidth];
    }

    public function next()
    {
        $this->nextLine();
    }

    public function key()
    {
        return $this->currentWordPosition;
    }

    public function valid()
    {
        return $this->currentWordPosition < \count($this->words);
    }

    public function rewind()
    {
        $this->currentWordPosition = $this->startWordPosition;
        $this->nextLine();
    }

    private function nextLine()
    {
        $nextWord = $this->words[$this->currentWordPosition];
        $currentWidth = $this->sizer->getWidth($nextWord);
        $currentWords = $nextWord;

        while (true) {
            // check if next word exists
            if (++$this->currentWordPosition === \count($this->words)) {
                break;
            }

            // check if next word fits
            $nextWord = $this->words[$this->currentWordPosition];
            $nextWidth = $this->sizer->getSpaceWidth() + $this->sizer->getWidth($nextWord);
            if ($currentWidth + $nextWidth > $this->width) {
                --$this->currentWordPosition;
                break;
            }

            // add next word
            $currentWords .= ' ' . $nextWord;
            $currentWidth += $nextWidth;
        }

        $this->currentWords = $currentWords;
        $this->currentWidth = $currentWidth;
    }
}
