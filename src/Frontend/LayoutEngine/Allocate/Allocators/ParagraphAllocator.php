<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators;

use PdfGenerator\Frontend\Content\Paragraph;
use PdfGenerator\Frontend\Resource\Font\FontMeasurement;
use PdfGenerator\Frontend\Resource\Font\FontRepository;

class ParagraphAllocator
{
    private FontRepository $fontRepository;

    public function __construct(private readonly float $width, private readonly float $height)
    {
        $this->fontRepository = FontRepository::instance();
    }

    public function allocate(Paragraph $paragraph, Paragraph &$overflow = null, float &$usedHeight = 0, float &$usedWidth = 0): ?Paragraph
    {
        $pendingPhrases = $paragraph->getPhrases();
        $allocatedPhrases = $this->allocatePhrases($pendingPhrases, $usedWidth, $usedHeight);

        if (0 === count($allocatedPhrases)) {
            return null;
        }

        $allocated = $paragraph->cloneWithPhrases($allocatedPhrases);
        $overflow = count($pendingPhrases) > 0 ? $paragraph->cloneWithPhrases($pendingPhrases) : null;

        return $allocated;
    }

    /**
     * @param Paragraph\Phrase[] $pendingPhrases
     *
     * @return Paragraph\Phrase[]
     */
    private function allocatePhrases(array &$pendingPhrases, int &$usedWidth, int &$usedHeight): array
    {
        $usedLineWidth = 0;
        $usedLineHeight = 0;
        /** @var Paragraph\Phrase[] $allocatedPhrases */
        $allocatedPhrases = [];
        while (count($pendingPhrases) > 0) {
            $phrase = $pendingPhrases[0];
            $textStyle = $phrase->getTextStyle();
            $fontMeasurement = $this->fontRepository->getFontMeasurement($textStyle);

            $availableHeight = $this->height - $usedHeight;
            $currentLineHeight = max($usedLineHeight, $fontMeasurement->getLeading());
            $maxLineCount = (int) ((($availableHeight - $currentLineHeight) / $fontMeasurement->getLeading()) + 1);
            assert($availableHeight > $currentLineHeight || 0 === $maxLineCount);

            $pendingLines = $phrase->getLines();
            $allocatedLines = $this->allocateLines($maxLineCount, $fontMeasurement, $pendingLines, $usedLineWidth, $usedWidth);

            if (count($allocatedLines) > 0) {
                $allocatedPhrases[] = Paragraph\Phrase::createFromLines($allocatedLines, $textStyle);
                $usedLineHeight = $currentLineHeight;

                if (count($pendingLines) > 0 || count($allocatedLines) > 1) {
                    $usedHeight += $usedLineHeight;
                    $usedLineHeight = $fontMeasurement->getLeading();
                    if (count($allocatedLines) > 1) {
                        $usedHeight += (count($allocatedLines) - 2) * $fontMeasurement->getLeading();
                    }

                    $phrase = Paragraph\Phrase::createFromLines($pendingLines, $textStyle);
                    $pendingPhrases[0] = $phrase;
                } else {
                    array_shift($pendingPhrases);
                }
            } else {
                break;
            }
        }

        if (count($allocatedPhrases) > 0 && 0 === count($pendingPhrases)) {
            $usedHeight += $usedLineHeight;
        }

        return $allocatedPhrases;
    }

    /**
     * @param string[] $pendingLines
     *
     * @return string[]
     */
    private function allocateLines(int $maxLines, FontMeasurement $fontMeasurement, array &$pendingLines, float &$usedLineWidth, float &$usedWidth): array
    {
        /** @var string[] $allocatedLines */
        $allocatedLines = [];
        while (count($pendingLines) > 0 && count($allocatedLines) < $maxLines) {
            $line = $pendingLines[0];
            $pendingWords = explode(' ', $line);
            while (count($pendingWords) > 0 && count($allocatedLines) < $maxLines) {
                $allocatedWords = $this->allocatedWords($fontMeasurement, $pendingWords, $usedLineWidth);

                // force at least a single word to be printed
                $noProgress = 0 === count($allocatedWords) && 0.0 === $usedLineWidth;
                if ($noProgress) {
                    $firstWord = array_shift($pendingWords);
                    $allocatedWords[] = $firstWord;
                    $usedLineWidth = $fontMeasurement->getWidth($firstWord);
                }

                $allocatedLine = implode(' ', $allocatedWords);
                $allocatedLines[] = $allocatedLine;

                // begin new line if possible
                if (count($pendingWords) > 0 && count($allocatedLines) < $maxLines) {
                    $usedWidth = max($usedWidth, $usedLineWidth);
                    $usedLineWidth = 0;
                }
            }

            if (count($pendingWords) > 0) {
                $pendingLines[0] = implode(' ', $pendingWords);
            } else {
                array_shift($pendingLines);
                if (count($pendingLines) > 0) {
                    $usedWidth = max($usedWidth, $usedLineWidth);
                    $usedLineWidth = 0;
                }
            }
        }

        if (count($allocatedLines) > 0) {
            $usedWidth = max($usedWidth, $usedLineWidth);
        }

        return $allocatedLines;
    }

    private function allocatedWords(FontMeasurement $fontMeasurement, array &$pendingWords, float &$usedLineWidth): array
    {
        /** @var string[] $allocatedWords */
        $allocatedWords = [];

        while (count($pendingWords) > 0) {
            $word = $pendingWords[0];
            $wordSize = $fontMeasurement->getWidth($word);
            $availableWidth = $this->width - $usedLineWidth;
            if ($wordSize < $availableWidth) {
                $allocatedWords[] = $word;
                $usedLineWidth += $wordSize;
                array_shift($pendingWords);

                // more words follow; add space
                if (count($pendingWords) > 0) {
                    $usedLineWidth += $fontMeasurement->getSpaceWidth();
                }
            } else {
                // natural line break; remove space at the end
                if (count($allocatedWords) > 0) {
                    $usedLineWidth -= $fontMeasurement->getSpaceWidth();
                }
                break;
            }
        }

        return $allocatedWords;
    }
}
