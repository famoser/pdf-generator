<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR;

use PdfGenerator\IR\Layout\Column;
use PdfGenerator\IR\Layout\Layout;
use PdfGenerator\IR\Printer\CursorGetSetTrait;
use PdfGenerator\IR\Printer\StyleGetSetTrait;
use PdfGenerator\IR\Structure\Document;
use PdfGenerator\IR\Structure\Document\Image;
use PdfGenerator\IR\Text\LineBreak\ScaledColumnBreaker;
use PdfGenerator\IR\Text\LineBreak\WordSizer\WordSizerRepository;

class FlowPrinter
{
    use CursorGetSetTrait;
    use StyleGetSetTrait;

    /**
     * @var CursorAwarePrinter
     */
    private $printer;

    /**
     * @var Layout
     */
    private $layout;

    /**
     * @var Column
     */
    private $activeColumn;

    /**
     * @var WordSizerRepository
     */
    private $wordSizerRepository;

    public function __construct(Document $document, Layout $layout)
    {
        $this->printer = new CursorAwarePrinter($document);
        $this->layout = $layout;

        $this->nextColumn();
        $this->wordSizerRepository = new WordSizerRepository();
    }

    public function getPrinter(): CursorAwarePrinter
    {
        return $this->printer;
    }

    public function nextLine(float $height, float $nextColumnHeight = null)
    {
        $cursor = $this->getCursor();
        $cursor = $cursor->withXCoordinate($this->activeColumn->getStart()->getXCoordinate());

        if (!$this->activeColumn->hasVerticalSpaceFor($cursor, $height)) {
            $this->nextColumn();
            $this->nextLine($nextColumnHeight ?? $height);
        } else {
            $cursor = $cursor->moveDown($height);
            $this->setCursor($cursor);
        }

        return $cursor;
    }

    public function nextColumn()
    {
        $this->activeColumn = $this->layout->getNextColumn();
        $cursor = $this->activeColumn->getStart();
        $this->setCursor($cursor);

        return $cursor;
    }

    public function printParagraph(string $text)
    {
        $textStyle = $this->getTextStyle();
        $wordSizer = $this->wordSizerRepository->getWordSizer($textStyle->getFont());
        $columnBreaker = new ScaledColumnBreaker($textStyle, $wordSizer, $text);

        $this->printTextBlock($columnBreaker);
    }

    public function continueParagraph(string $text)
    {
        $textStyle = $this->getTextStyle();
        $wordSizer = $this->wordSizerRepository->getWordSizer($textStyle->getFont());
        $columnBreaker = new ScaledColumnBreaker($textStyle, $wordSizer, $text);

        // if not just at column break, remove descender
        if (!$this->activeColumn->getStart()->equals($this->getCursor())) {
            $descender = $textStyle->getDescender();
            $this->moveDown($descender);
        }

        $this->printTextLine($columnBreaker);
        $lineGap = $textStyle->getLineGap();
        $this->moveDown($lineGap);

        $this->printTextBlock($columnBreaker);
    }

    private function printTextLine(ScaledColumnBreaker $columnBreaker)
    {
        $cursor = $this->getCursor();
        $availableWidth = $this->activeColumn->getAvailableWidth($cursor);

        // finish started line
        [$line, $lineWidth] = $columnBreaker->nextLine($availableWidth, false);
        $this->printer->printText($line);

        $textStyle = $this->getTextStyle();
        $descender = $textStyle->getDescender();
        $this->moveRightDown($lineWidth, -$descender);
    }

    private function printTextBlock(ScaledColumnBreaker $columnBreaker)
    {
        $width = $this->activeColumn->getWidth();

        $textStyle = $this->getTextStyle();
        $leading = $textStyle->getLeading();
        $ascender = $textStyle->getAscender();

        $cursor = $this->nextLine($ascender);

        // print columns until out of text
        do {
            $maxLines = (int)$this->activeColumn->countSpaceFor($cursor, $leading) + 1;
            [$lines, $lineWidths] = $columnBreaker->nextColumn($width, $maxLines);
            $text = implode("\n", $lines);
            $this->printer->printText($text);

            $lastLineWidth = $lineWidths[array_key_last($lineWidths)];
            $height = (\count($lines) - 1) * $leading;
            $cursor = $this->moveRightDown($lastLineWidth, $height);

            if ($columnBreaker->hasMoreLines()) {
                $this->nextColumn();
                $cursor = $this->nextLine($ascender);
            }
        } while ($columnBreaker->hasMoreLines());

        $textStyle = $this->getTextStyle();
        $descender = $textStyle->getDescender();
        $this->moveDown(-$descender);
    }

    public function printImage(Image $image, float $width, float $height)
    {
        $printImage = function () use ($image, $width, $height) {
            $this->printer->printImage($image, $width, $height);
        };
        $this->printFixedSizeBlock($printImage, $width, $height);
    }

    public function printRectangle(float $width, float $height)
    {
        $printImage = function () use ($width, $height) {
            $this->printer->printRectangle($width, $height);
        };
        $this->printFixedSizeBlock($printImage, $width, $height);
    }

    private function printFixedSizeBlock(callable $print, float $width, float $height)
    {
        $cursor = $this->getCursor();
        // start newline if:
        if ($this->activeColumn->getStart()->equals($cursor) || // totally new column
            !$this->activeColumn->hasHorizontalSpaceFor($cursor, $width)) { // not enough horizontal space
            $this->nextLine($height);
        }

        $print();

        $this->moveRight($width);
    }
}
