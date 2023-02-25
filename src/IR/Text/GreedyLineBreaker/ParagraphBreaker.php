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

use PdfGenerator\IR\Buffer\TextBuffer\MeasuredParagraph;
use PdfGenerator\IR\Printer\Line;
use PdfGenerator\IR\Structure\Document\Page\Content\Text\TextStyle;

class ParagraphBreaker
{
    /**
     * @var MeasuredParagraph
     */
    private $paragraph;

    /**
     * the next to be included word.
     *
     * @var int
     */
    private $nextPhraseIndex = 0;

    /**
     * @var PhraseBreaker|null
     */
    private $phraseBreaker;

    public function __construct(MeasuredParagraph $paragraph)
    {
        $this->paragraph = $paragraph;
    }

    public function isEmpty(): bool
    {
        return (null === $this->phraseBreaker || $this->phraseBreaker->isEmpty()) &&
            $this->nextPhraseIndex >= \count($this->paragraph->getMeasuredPhrases());
    }

    private function advancePhraseBreakerIfRequired()
    {
        if (null === $this->phraseBreaker || $this->phraseBreaker->isEmpty()) {
            $nextPhrase = $this->paragraph->getMeasuredPhrases()[$this->nextPhraseIndex++];
            $this->phraseBreaker = new PhraseBreaker($nextPhrase);
        }
    }

    private function nextTextStyle(): TextStyle
    {
        $this->advancePhraseBreakerIfRequired();

        return $this->phraseBreaker->getPhrase()->getTextStyle();
    }

    public function nextLine(float $targetWidth, bool $allowEmpty): Line
    {
        \assert(!$this->isEmpty());

        $currentWidth = 0;
        $line = new Line($this->nextTextStyle());
        while ($currentWidth < $targetWidth) {
            $this->advancePhraseBreakerIfRequired();

            $availableWidth = $targetWidth - $currentWidth;
            $fragment = $this->phraseBreaker->nextFragment($availableWidth, $allowEmpty);
            $currentWidth += $fragment->getWidth();
            $line->addFragment($fragment);

            if (!$this->phraseBreaker->isEmpty()) {
                // phrase breaker did not output all its contents, hence available width was not enough
                break;
            }

            // phrase breaker empty
            $this->phraseBreaker = null;
            if ($this->isEmpty()) {
                break;
            }

            $allowEmpty = true;
        }

        return $line;
    }

    /**
     * @return Line[]
     */
    public function nextLines(float $targetWidth, float $targetHeight, float $indent, bool $newParagraph): array
    {
        $allowEmpty = !$newParagraph; // if new paragraph force content on first line, else do not
        $requestedWidth = $targetWidth - $indent;

        $lines = [];
        $availableHeight = $targetHeight;
        while (true) {
            if ($this->isEmpty()) {
                break;
            }

            $availableHeight -= $this->nextTextStyle()->getLeading();
            if ($availableHeight < 0) {
                break;
            }

            $lines[] = $this->nextLine($requestedWidth, $allowEmpty);

            // while first line may have indent, further lines do not
            $allowEmpty = false;
            $requestedWidth = $targetWidth;
        }

        return $lines;
    }
}
