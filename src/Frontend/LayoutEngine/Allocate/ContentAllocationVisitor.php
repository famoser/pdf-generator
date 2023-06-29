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
use PdfGenerator\FrontendResources\CursorPrinter\Buffer\TextBuffer\MeasuredLine;
use PdfGenerator\FrontendResources\CursorPrinter\Buffer\TextBuffer\MeasuredParagraph;
use PdfGenerator\FrontendResources\CursorPrinter\Buffer\TextBuffer\MeasuredPhrase;

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

        $availableHeight = $this->height;
        foreach ($paragraph->getPhrases() as $phrase) {
            $textStyle = $phrase->getTextStyle();
            $font = $fontRepository->getFont($textStyle->getFont());

            $fontMeasurement = new FontMeasurement($font, $textStyle->getFontSize(), $textStyle->getLineHeight());
            if (!$availableHeight < $fontMeasurement->getLeading()) {
                break;
            }

            $sizer = $wordSizerRepository->getWordSizer($font);
            $lines = $this->splitAtNewlines($phrase->getText());
        }
    }

    public function getMeasuredParagraph(): MeasuredParagraph
    {
        $measuredParagraph = new MeasuredParagraph();

        foreach ($this->phrases as $phrase) {
            $textStyle = $phrase->getTextStyle();

            $measuredLines = [];
            $lines = $this->splitAtNewlines($phrase->getText());
            $sizer = $this->wordSizerRepository->getWordSizer($textStyle->getFont());
            foreach ($lines as $line) {
                $measuredLines[] = $this->measureLine($line, $sizer);
            }

            $measuredPhrase = new MeasuredPhrase($measuredLines, $textStyle);
            $measuredParagraph->addMeasuredPhrase($measuredPhrase);
        }

        return $measuredParagraph;
    }

    private function measureLine(string $line, WordSizerInterface $sizer): MeasuredLine
    {
        $words = explode(' ', $line);

        $wordWidths = [];
        foreach ($words as $word) {
            $wordWidths[] = $sizer->getWidth($word);
        }

        return new MeasuredLine($words, $wordWidths, $sizer->getSpaceWidth());
    }

    /**
     * @return string[]
     */
    private function splitAtNewlines(string $text): array
    {
        $textWithNormalizedNewlines = str_replace(["\r\n", "\n\r", "\r"], "\n", $text);

        return explode("\n", $textWithNormalizedNewlines);
    }
}
