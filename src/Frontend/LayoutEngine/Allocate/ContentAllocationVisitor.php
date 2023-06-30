<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Allocate;

use PdfGenerator\Frontend\Content\Paragraph;
use PdfGenerator\Frontend\Content\Rectangle;
use PdfGenerator\Frontend\Font\FontMeasurement;
use PdfGenerator\Frontend\Font\FontRepository;
use PdfGenerator\Frontend\Font\WordSizer\WordSizerInterface;
use PdfGenerator\Frontend\Font\WordSizer\WordSizerRepository;
use PdfGenerator\Frontend\LayoutEngine\AbstractContentVisitor;

/**
 * This allocates content on the PDF.
 *
 * All allocated content fits
 *
 * @implements AbstractContentVisitor<ContentAllocation|null>
 */
class ContentAllocationVisitor extends AbstractContentVisitor
{
    public function __construct(private readonly float $width, private readonly float $height)
    {
    }

    public function visitRectangle(Rectangle $rectangle): ?ContentAllocation
    {
        return new ContentAllocation($this->width, $this->height, $rectangle);
    }

    public function visitParagraph(Paragraph $paragraph): ?ContentAllocation
    {
        $wordSizerRepository = WordSizerRepository::instance();
        $fontRepository = FontRepository::instance();

        $usedLineWidth = 0;
        $usedLineHeight = 0;
        $usedHeight = 0;
        $usedWidth = 0;
        $pendingPhrases = $paragraph->getPhrases();
        /** @var Paragraph\Phrase[] $allocatedPhrases */
        $allocatedPhrases = [];
        while (count($pendingPhrases) > 0) {
            $phrase = $pendingPhrases[0];
            $textStyle = $phrase->getTextStyle();
            $font = $fontRepository->getFont($textStyle->getFont());
            $fontMeasurement = new FontMeasurement($font, $textStyle->getFontSize(), $textStyle->getLineHeight());
            $sizer = $wordSizerRepository->getWordSizer($font);

            $availableHeight = $this->height - $usedHeight;
            $currentLineHeight = max($usedLineHeight, $fontMeasurement->getLeading());
            $maxLineCount = (int) ((($availableHeight - $currentLineHeight) / $fontMeasurement->getLeading()) + 1);
            assert($availableHeight > $currentLineHeight || 0 === $maxLineCount);

            $pendingLines = $phrase->getLines();
            $allocatedLines = $this->allocateLines($maxLineCount, $sizer, $pendingLines, $usedLineWidth, $usedWidth);

            if (count($allocatedLines) > 0) {
                $usedLineHeight = $currentLineHeight;
                if (count($allocatedLines) > 1) {
                    $usedHeight += $usedLineHeight;
                    $usedLineHeight = $fontMeasurement->getLeading();
                    $usedHeight += (count($allocatedLines) - 2) * $fontMeasurement->getLeading();
                }

                $allocatedPhrases[] = Paragraph\Phrase::createFromLines($allocatedLines, $textStyle);
                if (count($pendingLines) > 0) {
                    $phrase = Paragraph\Phrase::createFromLines($pendingLines, $textStyle);
                    $pendingPhrases[0] = $phrase;
                } else {
                    array_shift($pendingPhrases);
                }
            } else {
                break;
            }
        }

        if (0 === count($allocatedPhrases)) {
            return null;
        }

        $allocated = $paragraph->cloneWithPhrases($allocatedPhrases);
        $overflow = count($pendingPhrases) > 0 ? $paragraph->cloneWithPhrases($pendingPhrases) : null;

        return new ContentAllocation($usedWidth, $usedHeight, $allocated, $overflow);
    }

    /**
     * @param string[] $pendingLines
     *
     * @return string[]
     */
    private function allocateLines(int $maxLines, WordSizerInterface $sizer, array &$pendingLines, float &$usedLineWidth, float &$usedWidth): array
    {
        /** @var string[] $allocatedLines */
        $allocatedLines = [];
        while (count($pendingLines) > 0 && count($allocatedLines) < $maxLines) {
            $line = $pendingLines[0];
            $pendingWords = explode(' ', $line);
            while (count($pendingWords) > 0 && count($allocatedLines) < $maxLines) {
                $allocatedWords = $this->allocatedWords($sizer, $pendingWords, $usedLineWidth);

                // force at least a single word to be printed
                $noProgress = 0 === count($allocatedWords) && 0 === $usedLineWidth;
                if ($noProgress) {
                    $firstWord = array_shift($pendingWords);
                    $allocatedWords[] = $firstWord;
                    $usedLineWidth = $sizer->getWidth($firstWord);
                }

                $allocatedLine = implode(' ', $allocatedWords);
                $allocatedLines[] = $allocatedLine;

                // begin new line
                if (count($pendingWords) > 0) {
                    $usedWidth = max($usedWidth, $usedLineWidth);
                    $usedLineWidth = 0;
                }
            }

            if (count($pendingWords) > 0) {
                $pendingLines[0] = implode(' ', $pendingWords);
            } else {
                array_shift($pendingLines);
            }
        }

        return $allocatedLines;
    }

    private function allocatedWords(WordSizerInterface $sizer, array &$pendingWords, float &$usedLineWidth): array
    {
        /** @var string[] $allocatedWords */
        $allocatedWords = [];

        while (count($pendingWords) > 0) {
            $word = $pendingWords[0];
            $wordSize = $sizer->getWidth($word);
            $availableWidth = $this->width - $usedLineWidth;
            if ($wordSize < $availableWidth) {
                $allocatedWords[] = $word;
                $usedLineWidth += $wordSize;
                array_shift($pendingWords);

                // more words follow; add space
                if (count($pendingWords) > 0) {
                    $usedLineWidth += $sizer->getSpaceWidth();
                }
            } else {
                // natural line break; remove space at the end
                if (count($allocatedWords) > 0) {
                    $usedLineWidth -= $sizer->getSpaceWidth();
                }
                break;
            }
        }

        return $allocatedWords;
    }
}
