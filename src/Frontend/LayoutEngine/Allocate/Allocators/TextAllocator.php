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

use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Content\Text\TextLine;
use Famoser\PdfGenerator\Frontend\Content\Text\TextSegment;
use Famoser\PdfGenerator\Frontend\Content\TextBlock;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\Layout\Text\Alignment;
use Famoser\PdfGenerator\Frontend\Layout\TextSpan;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontRepository;

readonly class TextAllocator
{
    private FontRepository $fontRepository;

    public function __construct(private float $width, private float $height)
    {
        $this->fontRepository = FontRepository::instance();
    }

    /**
     * @param TextSpan[] $overflowSpans
     */
    public function allocate(Text $text, array &$overflowSpans = [], float &$usedWidth = 0, float &$usedHeight = 0): TextBlock
    {
        $allocatedLines = [];
        $usedHeight = 0.0;
        $usedWidth = 0.0;
        $overflowSpans = $text->getSpans();
        while (count($overflowSpans) > 0) {
            $allocatedLineWidth = 0.0;
            $remainingSpans = [];
            $line = $this->allocateLine($text->getAlignment(), $this->width, $overflowSpans, $allocatedLineWidth, $remainingSpans);

            // cannot allocate, too high
            if ($usedHeight + $line->getLeading() > $this->height && count($allocatedLines) > 0) {
                break;
            }

            $allocatedLines[] = $line;
            $usedHeight += $line->getLeading();
            $usedWidth = max($usedWidth, $allocatedLineWidth);
            $overflowSpans = $remainingSpans;
        }

        return new TextBlock($usedWidth, $usedHeight, $allocatedLines);
    }

    /**
     * @param TextSpan[] $spans
     * @param TextSpan[] $overflow
     */
    private function allocateLine(Alignment $alignment, float $maxWidth, array $spans, float &$allocatedWidth, array &$overflow = []): TextLine
    {
        $overflow = $spans;
        $allocatedSegments = [];
        $leading = 0.0;
        $abortedByNewline = false;
        while ($span = array_shift($overflow)) {
            // allocate next segment
            $availableWidth = $maxWidth - $allocatedWidth;
            $nextLines = '';
            $line = self::getLine($span->getText(), $nextLines);
            $allocatedLineWidth = 0.0;
            $segment = $this->allocateSegment($span->getTextStyle(), $availableWidth, $line, $allocatedLineWidth, $overflowLine);

            // cannot allocate, too wide
            if ($allocatedWidth + $allocatedLineWidth > $maxWidth && count($allocatedSegments) > 0) {
                array_unshift($overflow, $span);
                break;
            }

            $allocatedSegments[] = $segment;
            $allocatedWidth += $allocatedLineWidth;

            // set leading
            $fontMeasurement = $this->fontRepository->getFontMeasurement($span->getTextStyle());
            $leading = max($leading, $fontMeasurement->getLeading());

            // set overflow
            if ($overflowLine !== '' || $nextLines !== '') {
                $remainingText = $overflowLine;

                // remove first space to logically replace space with (omitted) newline
                if (str_starts_with($remainingText, ' ')) {
                    $remainingText = substr($remainingText, 1);
                }

                if ($nextLines !== '' && $remainingText !== '') {
                    $remainingText .= "\n";
                }

                $remainingText .= $nextLines;

                $span = new TextSpan($remainingText, $span->getTextStyle());
                array_unshift($overflow, $span);
            }

            // start next span if no overflow on line & no newline
            if ($nextLines === '' && $overflowLine === '') {
                continue;
            }

            // else abort
            $abortedByNewline = $nextLines !== '' && $overflowLine === '';
            break;
        }

        // handle alignment
        $offset = 0.0;
        $wordSpacing = 0.0;
        $remainingWidth = $maxWidth - $allocatedWidth;
        $allocatedWidth = $alignment === Alignment::ALIGNMENT_LEFT ? $allocatedWidth : $maxWidth;
        if ($alignment === Alignment::ALIGNMENT_CENTER) {
            $offset = $remainingWidth / 2;
        } else if ($alignment === Alignment::ALIGNMENT_RIGHT) {
            $offset = $remainingWidth;
        } else if (
            $alignment === Alignment::ALIGNMENT_JUSTIFIED &&
            !$abortedByNewline && // for newlines, do not justify
            count($overflow) > 0 // for last line in paragraph, do not justify
        ) {
            $totalSpaceWidth = 0.0;
            foreach ($allocatedSegments as $allocatedSegment) {
                $spacesCount = mb_substr_count($allocatedSegment->getText(), ' ');
                $fontMeasurement = $this->fontRepository->getFontMeasurement($span->getTextStyle());
                $spaceWidth = $fontMeasurement->getSpaceWidth();
                $totalSpaceWidth += $spaceWidth * $spacesCount;
            }

            if ($totalSpaceWidth > 0) {
                // -1 as 0 is normal space, -1 is no space, 1 is 2x space, 2 is 3x space, etc
                $wordSpacing = (($totalSpaceWidth + $remainingWidth) / $totalSpaceWidth) - 1;
            }
        }

        return new TextLine($allocatedSegments, $leading, $offset, $wordSpacing);
    }

    private function allocateSegment(TextStyle $textStyle, float $maxWidth, string $content, float &$allocatedWidth, string &$overflow = null): TextSegment
    {
        $fontMeasurement = $this->fontRepository->getFontMeasurement($textStyle);

        $overflow = $content;
        $allocatedText = '';
        while ($overflow !== '') {
            $nextChunks = '';
            $chunk = self::getChunk($overflow, $nextChunks);
            $chunkWidth = $fontMeasurement->getWidth($chunk);

            if ($allocatedWidth + $chunkWidth > $maxWidth && $allocatedText !== '') {
                break;
            }

            $allocatedText .= $chunk;
            $allocatedWidth += $chunkWidth;

            $overflow = $nextChunks;
        }

        return new TextSegment($allocatedText, $textStyle);
    }

    public static function getLine(string $value, string &$nextLines = ''): string
    {
        $cleanedText = str_replace("\r", "", $value); // ignore carriage return for now
        $singleLineEnd = mb_strpos($cleanedText, "\n");
        if ($singleLineEnd === false) {
            return $cleanedText;
        }

        $nextLines = mb_substr($cleanedText, $singleLineEnd + 1);
        return mb_substr($cleanedText, 0, $singleLineEnd);
    }

    public static function getChunk(string $value, string &$nextChunks = ''): string
    {
        $noPrefixValue = mb_ltrim($value);
        $chunkContentStart = mb_strlen($value) - mb_strlen($noPrefixValue);

        $chunkContentEnd = mb_strpos($value, ' ', $chunkContentStart);
        if ($chunkContentEnd === false) {
            return $value;
        }

        $nextChunks = mb_substr($value, $chunkContentEnd);
        return mb_substr($value, 0, $chunkContentEnd);
    }
}
