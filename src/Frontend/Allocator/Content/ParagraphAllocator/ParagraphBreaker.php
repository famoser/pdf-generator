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

use PdfGenerator\Frontend\LocatedContent\Paragraph\Line;
use PdfGenerator\Frontend\MeasuredContent\Paragraph;
use PdfGenerator\Frontend\MeasuredContent\Utils\FontMeasurement;

class ParagraphBreaker
{
    /**
     * @var Paragraph
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

    public function __construct(Paragraph $paragraph)
    {
        $this->paragraph = $paragraph;
    }

    public function isEmpty(): bool
    {
        return ($this->phraseBreaker === null || $this->phraseBreaker->isEmpty()) &&
            $this->nextPhraseIndex >= \count($this->paragraph->getPhrases());
    }

    private function advancePhraseBreakerIfRequired()
    {
        if ($this->phraseBreaker === null || $this->phraseBreaker->isEmpty()) {
            $nextPhrase = $this->paragraph->getPhrases()[$this->nextPhraseIndex++];
            $nextTextStyle = $nextPhrase->getTextStyle();
            $fontMeasurement = new FontMeasurement($nextPhrase->getFont(), $nextTextStyle->getFontSize(), $nextTextStyle->getLineHeight());
            $this->phraseBreaker = new PhraseBreaker($nextPhrase, $fontMeasurement);
        }
    }

    private function addFragments(Line $line, float $targetWidth, bool $allowEmpty)
    {
        \assert(!$this->isEmpty());

        $currentWidth = 0;
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
    }

    public function nextLines(float $targetWidth, float $targetHeight, float $indent, bool $newParagraph): array
    {
        $allowEmpty = !$newParagraph; // if new paragraph force content on first line, else do not
        $requestedWidth = $targetWidth - $indent;

        $lines = [];
        $currentHeight = 0;
        $maxWidth = 0;
        while (true) {
            if ($this->isEmpty()) {
                break;
            }

            $this->advancePhraseBreakerIfRequired();
            $leading = $this->phraseBreaker->getFontMeasurement()->getLeading();

            if ($currentHeight + $leading > $targetHeight) {
                break;
            }

            $line = new Line($leading);
            $this->addFragments($line, $requestedWidth, $allowEmpty);
            $lines[] = $line;
            $currentHeight += $leading;
            $maxWidth = max($maxWidth, $line->getWidth());

            // while first line may have indent, further lines do not and hence must not be empty
            $allowEmpty = false;
            $requestedWidth = $targetWidth;
        }

        return [$lines, $maxWidth, $currentHeight];
    }

    public function widthEstimate()
    {
        $maxWidth = 0;
        foreach ($this->paragraph->getPhrases() as $phrase) {
            foreach ($phrase->getMeasuredLines() as $measuredLine) {
                $maxWidth = max($measuredLine->getWidth(), $maxWidth);
            }
        }

        return $maxWidth;
    }
}
