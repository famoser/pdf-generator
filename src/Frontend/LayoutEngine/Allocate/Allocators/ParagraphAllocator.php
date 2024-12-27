<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators;

use Famoser\PdfGenerator\Frontend\Content\Paragraph;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontMeasurement;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontRepository;

readonly class ParagraphAllocator
{
    private FontRepository $fontRepository;

    public function __construct(private float $width, private float $height)
    {
        $this->fontRepository = FontRepository::instance();
    }

    public function allocate(Paragraph $paragraph, ?Paragraph &$overflow = null, float &$usedHeight = 0.0, float &$usedWidth = 0.0): Paragraph
    {
        $pendingPhrases = [];
        $allocatedPhrases = $this->allocatedParagraph($paragraph->getPhrases(), $usedWidth, $usedHeight, $pendingPhrases);

        $allocated = $paragraph->cloneWithPhrases($allocatedPhrases);
        $overflow = count($pendingPhrases) > 0 ? $paragraph->cloneWithPhrases($pendingPhrases) : null;

        return $allocated;
    }

    /**
     * @param Paragraph\Phrase[] $phrases
     * @param Paragraph\Phrase[] $pendingPhrases
     *
     * @return Paragraph\Phrase[]
     */
    private function allocatedParagraph(array $phrases, float &$usedWidth, float &$usedHeight, array &$pendingPhrases): array
    {
        $pendingPhrases = $phrases;
        /** @var Paragraph\Phrase[] $allocatedPhrases */
        $allocatedPhrases = [];

        $currentOffset = 0;
        $currentLineHeight = 0;

        $usedHeight = 0.0;
        $usedWidth = 0.0;
        while (count($pendingPhrases) > 0) {
            $phrase = array_shift($pendingPhrases);

            $fontMeasurement = $this->fontRepository->getFontMeasurement($phrase->getTextStyle());
            $availableHeight = $this->height - $usedHeight;
            $previousLeadingAdjustment = min($fontMeasurement->getLeading(), $currentLineHeight);
            $availableHeight += $previousLeadingAdjustment; // continue on previous line
            $availableLineCount = (int) ($availableHeight / $fontMeasurement->getLeading());

            $allocatedUsedWidth = 0;
            $lastLineOffset = 0;
            $pendingLines = [];
            $allocatedLines = self::allocatePhrase($fontMeasurement, $phrase->getLines(), $this->width, $availableLineCount, $currentOffset, $allocatedUsedWidth, $lastLineOffset, $pendingLines);

            $progressMade = count($allocatedPhrases) > 0;
            $outOfBoundingBox = count($allocatedLines) > $availableLineCount || (1 === $availableLineCount && $usedWidth > $this->width);
            if ($progressMade && $outOfBoundingBox) {
                array_unshift($pendingPhrases, $phrase);
                break;
            }

            $allocatedPhrases[] = $phrase->cloneWithLines($allocatedLines);
            $usedHeight += 1 === count($allocatedPhrases) ? $fontMeasurement->getLeading() : 0;
            $usedHeight += (count($allocatedLines) - 1) * $fontMeasurement->getLeading();
            $usedWidth = max($usedWidth, $allocatedUsedWidth);

            $currentOffset = $lastLineOffset;
            $currentLineHeight = count($allocatedLines) > 1 ? $fontMeasurement->getLeading() : max($currentLineHeight, $fontMeasurement->getLeading());

            if (count($pendingLines) > 0) {
                array_unshift($pendingPhrases, $phrase->cloneWithLines($pendingLines));
                break;
            }
        }

        return $allocatedPhrases;
    }

    /**
     * @param string[] $lines
     * @param string[] $pendingLines
     *
     * @return string[]
     */
    private static function allocatePhrase(FontMeasurement $fontMeasurement, array $lines, float $availableWidth, int $maxLineCount, float $offset, float &$usedWidth, float &$lastLineOffset, array &$pendingLines): array
    {
        $pendingLines = $lines;
        /** @var string[] $allocatedLines */
        $allocatedLines = [];
        $currentLineWidth = $offset;
        $lastLineOffset = $offset;
        while (count($pendingLines) > 0) {
            $pendingLine = array_shift($pendingLines);
            $words = explode(' ', (string) $pendingLine);

            $availableLineWidth = $availableWidth - $currentLineWidth;
            $pendingWords = [];
            $allocatedWidth = 0;
            $allocatedWords = self::allocatedWords($fontMeasurement, $availableLineWidth, $words, $allocatedWidth, $pendingWords);

            $outOfBoundingBox = $allocatedWidth > $availableLineWidth;
            $nextLineHasMoreSpace = $availableLineWidth < $availableWidth;
            $progressOptional = $maxLineCount > 1 || count($allocatedLines) > 0;
            if ($outOfBoundingBox && $nextLineHasMoreSpace && $progressOptional) {
                array_unshift($pendingLines, $pendingLine);
                $allocatedLines[] = '';
                $currentLineWidth = 0;
                continue;
            }

            $allocatedLines[] = implode(' ', $allocatedWords);
            $usedWidth = max($usedWidth, $currentLineWidth + $allocatedWidth);
            $lastLineOffset = $currentLineWidth + $allocatedWidth;
            $currentLineWidth = 0;

            // re-add line if more words, proceed to next line
            if (count($pendingWords) > 0) {
                array_unshift($pendingLines, implode(' ', $pendingWords));
            }

            if (count($allocatedLines) >= $maxLineCount) {
                break;
            }
        }

        return $allocatedLines;
    }

    /**
     * @param string[] $words
     * @param string[] $pendingWords
     *
     * @return string[]
     */
    private static function allocatedWords(FontMeasurement $fontMeasurement, float $availableWidth, array $words, float &$width, array &$pendingWords): array
    {
        $pendingWords = $words;
        /** @var string[] $allocatedWords */
        $allocatedWords = [];
        $width = 0.0;
        while (count($pendingWords) > 0) {
            $word = array_shift($pendingWords);
            $wordSize = $fontMeasurement->getWidth($word);
            $widthAdvance = $wordSize + (count($allocatedWords) > 0 ? $fontMeasurement->getSpaceWidth() : 0);

            $progressMade = count($allocatedWords) > 0;
            $outOfBoundingBox = $availableWidth < ($width + $widthAdvance);
            if ($progressMade && $outOfBoundingBox) {
                array_unshift($pendingWords, $word);
                break;
            }

            $allocatedWords[] = $word;
            $width += $widthAdvance;
        }

        return $allocatedWords;
    }
}
