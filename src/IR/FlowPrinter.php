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

use PdfGenerator\IR\Layout\ColumnGenerator;
use PdfGenerator\IR\Structure\Document;
use PdfGenerator\IR\Structure\Document\Image;
use PdfGenerator\IR\Text\LineBreak\ColumnBreaker;
use PdfGenerator\IR\Text\LineBreak\WordSizer\FontSizerRepository;

class FlowPrinter
{
    /**
     * @var Document
     */
    private $document;

    /**
     * @var CursorAwarePrinter
     */
    private $printer;

    /**
     * @var ColumnGenerator
     */
    private $columnGenerator;

    /**
     * @var int[]
     */
    private $margin = [25, 25, 25, 25];

    public function __construct(Document $document)
    {
        $this->document = $document;
        $this->printer = new CursorAwarePrinter($document);
        $this->printer->setTop(20);
        $this->printer->setLeft(20);

        $this->fontSizerRepository = new FontSizerRepository();
    }

    public function getPrinter(): CursorAwarePrinter
    {
        return $this->printer;
    }

    public function setMargin(float $top, float $right = null, float $bottom = null, float $left = null)
    {
        if (!$right) {
            $this->margin = [$top, $top, $top, $top];
        } elseif (!$bottom) {
            $this->margin = [$top, $right, $top, $right];
        } elseif (!$left) {
            $this->margin = [$top, $right, $bottom, $right];
        } else {
            $this->margin = [$top, $right, $bottom, $left];
        }
    }

    /**
     * ensures a block of specified height has space within column.
     * goes to next column if it does not fit (only once; the block is placed on the next column no matter its size).
     */
    private function placeBlock(float $height, float $nextPageHeight = null)
    {
        // TODO: check if within column, else advance column
        // TODO: both done with ColumnGenerator
        $this->printer->moveDown($height);

        if ($this->printer->getCursor()->getYCoordinate() < $this->margin[2]) {
            if ($nextPageHeight === null) {
                $nextPageHeight = $height;
            }

            $this->printer->advancePage();
            $this->getPrinter()->setTop($this->margin[0] + $nextPageHeight);
        }
    }

    /**
     * @var FontSizerRepository
     */
    private $fontSizerRepository;

    public function printParagraph(string $text)
    {
        $rectangleStyle = new Document\Page\Content\Rectangle\RectangleStyle(0.2, new Document\Page\Content\Common\Color(0, 255, 255), null);
        $this->getPrinter()->getPrinter()->setRectangleStyle($rectangleStyle);

        $textStyle = $this->getPrinter()->getPrinter()->getTextStyle();
        $fontSizer = $this->fontSizerRepository->getWordSizer($textStyle);

        $paragraphWidth = $this->margin[1] - $this->margin[3];

        $scaling = $textStyle->getFont()->getUnitsPerEm() / $textStyle->getFontSize();
        $columnBreaker = new ColumnBreaker($fontSizer, $text);
        [$lines, $lineWidths] = $columnBreaker->nextColumn(170 * $scaling, 10);

        $ascender = $textStyle->getFont()->getAscender() / $scaling;
        $this->getPrinter()->moveDown($ascender);

        $text = implode("\n", $lines);
        $this->printer->printText($text);

        $leading = $textStyle->getFont()->getBaselineToBaselineDistance() / $scaling * $textStyle->getLineHeight();
        $height = (\count($lines) - 1) * $leading;
        $descender = $textStyle->getFont()->getDescender() / $scaling;
        $this->getPrinter()->moveDown($height - $descender);

        $this->getPrinter()->printRectangle(170, $ascender + $height - $descender);
    }

    public function printImage(Image $image, float $width, float $height)
    {
        // TODO: check if space to the right, if yes, print, else start new line and print
        // TODO: if column is full, return error code
        $this->printer->moveDown($height);
        $this->printer->printImage($image, $width, $height);
        $this->printer->moveRight($width);
    }

    public function printRectangle(float $width, float $height)
    {
        // TODO: check if space to the right, if yes, print, else start new line and print
        // TODO: if column is full, return error code
        $this->printer->moveDown($height);
        $this->printer->printRectangle($width, $height);
        $this->printer->moveRight($width);
    }
}
