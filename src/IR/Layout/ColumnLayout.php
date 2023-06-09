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
use PdfGenerator\IR\Structure\Document\Page\Content\Rectangle\RectangleStyle;

class ColumnLayout
{
    private CursorPrinter $printer;

    private ColumnGenerator $columnGenerator;

    private Column $activeColumn;

    /**
     * ColumnLayout constructor.
     */
    public function __construct(CursorPrinter $printer, ColumnGenerator $columnGenerator)
    {
        $this->printer = $printer;

        $this->columnGenerator = $columnGenerator;
        $this->activeColumn = $columnGenerator->getNextColumn();
        $this->printer->setCursor($this->activeColumn->getStart());
    }

    public function addImage(Image $image, float $width, float $height)
    {
        $printImage = function (Cursor $cursor) use ($image, $width, $height) {
            $this->printer->printImage($cursor, $image, $width, $height);
        };
        $this->printFixedSizeBlock($printImage, $width, $height);
    }

    public function addRectangle(RectangleStyle $style, float $width, float $height)
    {
        $printImage = function (Cursor $cursor) use ($style, $width, $height) {
            $this->printer->setRectangleStyle($style);
            $this->printer->printRectangle($cursor, $width, $height);
        };
        $this->printFixedSizeBlock($printImage, $width, $height);
    }

    private function printFixedSizeBlock(callable $print, float $width, float $height)
    {
        $cursor = $this->printer->getCursor();
        // start newline if:
        if ($this->activeColumn->getStart()->equals($cursor) // totally new column
            || !$this->activeColumn->hasHorizontalSpaceFor($cursor, $width)) { // not enough horizontal space
            $cursor = $this->nextLine($cursor, $height);
        }

        $print($cursor);
    }

    private function resetLine(Cursor $cursor): Cursor
    {
        $newXCoordinate = $this->activeColumn->getStart()->getXCoordinate();

        return $cursor->withXCoordinate($newXCoordinate);
    }

    private function nextLine(Cursor $cursor, float $height, float $nextColumnHeight = null): Cursor
    {
        if (!$this->activeColumn->hasVerticalSpaceFor($cursor, $height)) {
            $cursor = $this->nextColumn();
            $cursor = $this->nextLine($cursor, $nextColumnHeight ?? $height);
        } else {
            $cursor = $cursor->moveDown($height);
            $cursor = $this->resetLine($cursor);
        }

        return $cursor;
    }

    public function nextColumn()
    {
        $this->activeColumn = $this->columnGenerator->getNextColumn();

        return $this->activeColumn->getStart();
    }

    public function addSpace(float $space)
    {
        $cursor = $this->printer->getCursor();
        $cursor = $this->nextLine($cursor, $space);
        $this->printer->setCursor($cursor);
    }
}
