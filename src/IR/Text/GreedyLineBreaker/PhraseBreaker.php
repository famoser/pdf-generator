<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Text\GreedyLineBreaker;

use PdfGenerator\IR\Buffer\TextBuffer\MeasuredPhrase;
use PdfGenerator\IR\Printer\Fragment;

class PhraseBreaker
{
    /**
     * @var MeasuredPhrase
     */
    private $phrase;

    /**
     * the next to be included word.
     *
     * @var int
     */
    private $nextLineIndex = 0;

    /**
     * @var LineBreaker|null
     */
    private $lineBreaker;

    public function __construct(MeasuredPhrase $phrase)
    {
        $this->phrase = $phrase;
    }

    public function getPhrase(): MeasuredPhrase
    {
        return $this->phrase;
    }

    public function isEmpty(): bool
    {
        return ($this->lineBreaker === null || $this->lineBreaker->isEmpty()) &&
            $this->nextLineIndex >= \count($this->phrase->getMeasuredLines());
    }

    public function nextFragment(float $targetWidth, bool $allowEmpty): ?Fragment
    {
        \assert(!$this->isEmpty());

        if ($this->lineBreaker === null || $this->lineBreaker->isEmpty()) {
            $nextLine = $this->phrase->getMeasuredLines()[$this->nextLineIndex++];
            $this->lineBreaker = new LineBreaker($nextLine);
        }

        $scale = $this->phrase->getTextStyle()->getFontScaling();
        $scaledWidth = $targetWidth * $scale;
        [$line, $width] = $this->lineBreaker->nextLine($scaledWidth, $allowEmpty);

        return new Fragment($line, $this->phrase->getTextStyle(), $width / $scale);
    }
}
