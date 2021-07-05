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
use PdfGenerator\IR\Structure\Document;
use PdfGenerator\IR\Structure\Document\Image;
use PdfGenerator\IR\Text\LineBreak\ColumnBreaker;
use PdfGenerator\IR\Text\LineBreak\WordSizer\FontSizerRepository;

class FlowPrinter
{
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

    public function __construct(Document $document, Layout $layout)
    {
        $this->printer = new CursorAwarePrinter($document);
        $this->layout = $layout;
        $this->fontSizerRepository = new FontSizerRepository();
    }

    public function getPrinter(): CursorAwarePrinter
    {
        return $this->printer;
    }

    private function isBlockWithinActiveColumn(float $height, float $right)
    {
        if ($this->activeColumn === null) {
            return false;
        }

        $afterLeft = $this->getPrinter()->getCursor()->getXCoordinate() + $right;
        $maxLeft = $this->activeColumn->getWidth() + $this->activeColumn->getStart()->getXCoordinate();
        if ($afterLeft > $maxLeft) {
            return false;
        }

        $afterTop = $this->getPrinter()->getCursor()->getYCoordinate() - $height;
        $minTop = $this->activeColumn->getStart()->getYCoordinate() - $this->activeColumn->getHeight();
        if ($afterTop < $minTop) {
            return false;
        }

        return true;
    }

    /**
     * ensures a block of specified height has space within column.
     * goes to next column if it does not fit (only once; the block is placed on the next column no matter its size).
     */
    private function placeBlock(float $height, float $nextColumnHeight = null)
    {
        if ($this->isBlockWithinActiveColumn($height, 0)) {
            $this->getPrinter()->moveDown($height);
        } else {
            $this->activeColumn = $this->layout->getNextColumn();
            $this->getPrinter()->setCursor($this->activeColumn->getStart());
            $this->getPrinter()->moveDown($nextColumnHeight ?? $height);
        }
    }

    /**
     * @var FontSizerRepository
     */
    private $fontSizerRepository;

    public function printParagraph(string $text)
    {
        $this->placeBlock(20);
        // TODO: improve height / width dealing -> images & rectangles deal with width, text does not

        // TODO: improve coordinate inversion owning
        //       a) place only in lower printer, but then needs to know about pages (better not)
        //       b) directly in layout, but then layouts more complex to implement

        $rectangleStyle = new Document\Page\Content\Rectangle\RectangleStyle(0.2, new Document\Page\Content\Common\Color(0, 255, 255), null);
        $this->getPrinter()->getPrinter()->setRectangleStyle($rectangleStyle);

        $textStyle = $this->getPrinter()->getPrinter()->getTextStyle();
        $fontSizer = $this->fontSizerRepository->getWordSizer($textStyle);

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

        $maxWidth = max($lineWidths);

        $this->getPrinter()->printRectangle($maxWidth / $scaling, $ascender + $height - $descender);
    }

    public function printImage(Image $image, float $width, float $height)
    {
        $this->placeBlock($height);
        $this->printer->printImage($image, $width, $height);
        $this->printer->moveRight($width);
    }

    public function printRectangle(float $width, float $height)
    {
        $this->placeBlock($height);
        $this->printer->printRectangle($width, $height);
        $this->printer->moveRight($width);
    }
}
