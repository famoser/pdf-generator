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
     * the next to be included word.
     */
    private int $nextPhraseIndex = 0;

    private ?PhraseBreaker $phraseBreaker = null;

    public function __construct(private readonly Paragraph $paragraph)
    {
    }

    public function isEmpty(): bool
    {
        return (null === $this->phraseBreaker || $this->phraseBreaker->isEmpty())
            && $this->nextPhraseIndex >= \count($this->paragraph->getPhrases());
    }

    private function advancePhraseBreakerIfRequired(): void
    {
        if (null === $this->phraseBreaker || $this->phraseBreaker->isEmpty()) {
            $nextPhrase = $this->paragraph->getPhrases()[$this->nextPhraseIndex++];
            $nextTextStyle = $nextPhrase->getTextStyle();
            $fontMeasurement = new FontMeasurement($nextPhrase->getFont(), $nextTextStyle->getFontSize(), $nextTextStyle->getLineHeight());
            $this->phraseBreaker = new PhraseBreaker($nextPhrase, $fontMeasurement);
        }
    }

    private function addFragments(Line $line, float $targetWidth, bool $allowEmpty): void
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
