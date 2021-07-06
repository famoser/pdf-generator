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
     * @var Document
     */
    private $document;

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
        $this->document = $document;
        $this->printer = new CursorAwarePrinter($document);
        $this->layout = $layout;
        $this->fontSizerRepository = new FontSizerRepository();

        $this->nextColumn();
    }

    public function getPrinter(): CursorAwarePrinter
    {
        return $this->printer;
    }

    /**
     * ensures a block of specified height has space within column.
     * goes to next column if it does not fit (only once; the block is placed on the next column no matter its size).
     */
    public function reserveHeight(float $height, float $nextColumnHeight = null)
    {
        $cursor = $this->getPrinter()->getCursor();

        if ($this->activeColumn !== null && $this->activeColumn->withinColumnHeight($cursor, $height)) {
            $cursor = $cursor->moveDown($height);
        } else {
            $cursor = $this->nextColumn();
            $cursor = $cursor->moveDown($nextColumnHeight);
        }

        $this->getPrinter()->setCursor($cursor);
    }

    public function nextColumn()
    {
        $this->activeColumn = $this->layout->getNextColumn();
        $cursor = $this->activeColumn->getStart();
        $this->getPrinter()->setCursor($cursor);

        return $cursor;
    }

    /**
     * @var FontSizerRepository
     */
    private $fontSizerRepository;

    public function printPhrase(string $text)
    {
        $cursor = $this->getPrinter()->getCursor();

        $width = $this->activeColumn->getWidth();
        $availableWidth = $this->activeColumn->getAvailableWidth($cursor);

        $textStyle = $this->getPrinter()->getPrinter()->getTextStyle();
        $fontSizer = $this->fontSizerRepository->getWordSizer($textStyle);
        $scaling = $textStyle->getFont()->getUnitsPerEm() / $textStyle->getFontSize();
        $leading = $textStyle->getFont()->getBaselineToBaselineDistance() / $scaling * $textStyle->getLineHeight();
        $ascender = $textStyle->getFont()->getAscender() / $scaling;
        $descender = $textStyle->getFont()->getDescender() / $scaling;

        $columnBreaker = new ColumnBreaker($fontSizer, $text);

        // finish started line
        if ($availableWidth < $width) {
            [$line, $lineWidth] = $columnBreaker->nextLine($availableWidth * $scaling);
            $this->printer->printText($line);

            if (!$columnBreaker->hasMoreLines()) {
                $cursor = $cursor->moveRight($lineWidth / $scaling)
                    ->moveDown(-$descender);
                $this->getPrinter()->setCursor($cursor);

                return;
            }

            $this->reserveHeight($leading, $ascender);

            $cursor = $this->getPrinter()->getCursor()->withXCoordinate($this->activeColumn->getStart()->getXCoordinate());
            $this->getPrinter()->setCursor($cursor);
        }

        // finish started column
        $maxLines = (int)$this->activeColumn->countSpaceFor($cursor, $leading) + 1;
        [$lines, $lineWidths] = $columnBreaker->nextColumn($width * $scaling, $maxLines);
        $text = implode("\n", $lines);
        $this->printer->printText($text);

        $lastLineWidth = $lineWidths[\count($lineWidths) - 1];
        $cursor = $cursor->moveRight($lastLineWidth / $scaling)
            ->moveDown((\count($lines) - 1) * $leading);
        $this->getPrinter()->setCursor($cursor);

        // print remaining text
        while ($columnBreaker->hasMoreLines()) {
            $cursor = $this->nextColumn();

            $maxLines = (int)$this->activeColumn->countSpaceFor($cursor, $leading) + 1;
            [$lines, $lineWidths] = $columnBreaker->nextColumn($width * $scaling, $maxLines);
            $text = implode("\n", $lines);
            $this->printer->printText($text);

            $lastLineWidth = $lineWidths[\count($lineWidths) - 1];
            $cursor = $cursor->moveRight($lastLineWidth / $scaling)
                ->moveDown((\count($lines) - 1) * $leading);
            $this->getPrinter()->setCursor($cursor);
        }
    }

    public function printImage(Image $image, float $width, float $height)
    {
        $this->reserveHeight($height);
        $this->printer->printImage($image, $width, $height);
        $this->moveRight($width);
    }

    public function printRectangle(float $width, float $height)
    {
        $this->reserveHeight($height);
        $this->printer->printRectangle($width, $height);
        $this->moveRight($width);
    }

    public function moveRight(float $width)
    {
        $cursor = $this->getPrinter()->getCursor();
        $cursor = $cursor->moveRight($width);
        $this->getPrinter()->setCursor($cursor);
    }

    public function moveDown(float $height)
    {
        $cursor = $this->getPrinter()->getCursor();
        $cursor = $cursor->moveDown($height);
        $this->getPrinter()->setCursor($cursor);
    }
}
