<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\IR\Layout;

use PdfGenerator\IR\Cursor;
use PdfGenerator\IR\CursorPrinter;
use PdfGenerator\IR\Layout\Column\Column;
use PdfGenerator\IR\Layout\Column\ColumnGenerator;
use PdfGenerator\IR\Structure\Document\Image;
use PdfGenerator\IR\Text\TextWriter;
use PdfGenerator\IR\Text\TextWriter\TextBlock;

class ColumnLayout
{
    /**
     * @var CursorPrinter
     */
    private $printer;

    /**
     * @var ColumnGenerator
     */
    private $columnGenerator;

    /**
     * @var Column
     */
    private $activeColumn;

    /**
     * ColumnLayout constructor.
     *
     * @param Column $activeColumn
     */
    public function __construct(CursorPrinter $printer, ColumnGenerator $columnGenerator)
    {
        $this->printer = $printer;

        $this->columnGenerator = $columnGenerator;
        $this->activeColumn = $columnGenerator->getNextColumn();
    }

    public function addParagraph(TextWriter $textWriter, float $indent = 0)
    {
        $cursor = $this->printer->getCursor();

        while (!$textWriter->isEmpty()) {
            $width = $this->activeColumn->getAvailableWidth($cursor);
            $height = $this->activeColumn->getAvailableHeight($cursor);

            $textBlock = $textWriter->getTextBlock($width, $height, $indent);
            $this->printTextBlock($textBlock);
        }
    }

    public function continueParagraph(TextWriter $textWriter, float $indent = 0)
    {
        $cursor = $this->printer->getCursor();

        // reposition cursor to previous baseline
        $currentIndent = $cursor->getXCoordinate() - $this->activeColumn->getStart()->getXCoordinate();
        $lineGap = $textWriter->getNextLineGap();
        $cursor = $cursor->moveRightDown(-$currentIndent, -$lineGap);

        $this->printer->setCursor($cursor);

        $this->addParagraph($textWriter, $currentIndent + $indent);
    }

    public function printTextBlock(TextBlock $textBlock)
    {
        $cursor = $this->printer->getCursor();
        $cursor = $cursor->moveRightDown($textBlock->getIndent(), $textBlock->getAscender());

        // baseline aligned
        foreach ($textBlock->getMeasuredPhrases() as $measuredPhrase) {
            $this->printer->setTextStyle($measuredPhrase->getTextStyle());

            $lines = $measuredPhrase->getLines();
            $lineWidths = $measuredPhrase->getLineWidths();

            if ($cursor->getXCoordinate() > $this->activeColumn->getStart()->getXCoordinate()) {
                // continue the current line
                $line = array_shift($lines);
                $lineWidth = array_shift($lineWidths);

                $this->printer->printText($cursor, $line);
                $cursor = $cursor->moveRight($lineWidth);
            }

            if (\count($lines) === 0) {
                continue;
            }

            $text = implode("\n", $lines);
            $cursor = $cursor->withXCoordinate($this->activeColumn->getStart()->getXCoordinate());
            $this->printer->printText($cursor, $text);

            $lastLineWidth = $measuredPhrase->getLineWidths()[\count($lines)];
            $height = (\count($lines) - 1) * $measuredPhrase->getTextStyle()->getLeading();
            $cursor = $cursor->moveRightDown($lastLineWidth, $height);
        }

        $cursor = $cursor->moveDown(-$textBlock->getDescender());
        $this->printer->setCursor($cursor);
    }

    public function addImage(Image $image, float $width, float $height)
    {
        $printImage = function (Cursor $cursor) use ($image, $width, $height) {
            $this->printer->printImage($cursor, $image, $width, $height);
        };
        $this->printFixedSizeBlock($printImage, $width, $height);
    }

    public function addRectangle(float $width, float $height)
    {
        $printImage = function (Cursor $cursor) use ($width, $height) {
            $this->printer->printRectangle($cursor, $width, $height);
        };
        $this->printFixedSizeBlock($printImage, $width, $height);
    }

    private function printFixedSizeBlock(callable $print, float $width, float $height)
    {
        $cursor = $this->printer->getCursor();
        // start newline if:
        if ($this->activeColumn->getStart()->equals($cursor) || // totally new column
            !$this->activeColumn->hasHorizontalSpaceFor($cursor, $width)) { // not enough horizontal space
            $cursor = $this->nextLine($cursor, $height);
        }

        $print($cursor);

        $cursor = $cursor->moveRight($width);
        $this->printer->setCursor($cursor);
    }

    public function nextLine(Cursor $cursor, float $height, float $nextColumnHeight = null)
    {
        if (!$this->activeColumn->hasVerticalSpaceFor($cursor, $height)) {
            $cursor = $this->nextColumn();
            $cursor = $this->nextLine($cursor, $nextColumnHeight ?? $height);
        } else {
            $cursor = $cursor->moveDown($height);
        }

        return $cursor;
    }

    public function nextColumn()
    {
        $this->activeColumn = $this->columnGenerator->getNextColumn();

        return $this->activeColumn->getStart();
    }
}
