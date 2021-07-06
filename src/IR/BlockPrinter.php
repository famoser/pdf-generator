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
use PdfGenerator\IR\Text\LineBreak\ScaledColumnBreaker;
use PdfGenerator\IR\Text\LineBreak\WordSizer\WordSizerRepository;

class BlockPrinter
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
        $this->wordSizerRepository = new WordSizerRepository();

        $this->nextColumn();
    }

    public function getPrinter(): CursorAwarePrinter
    {
        return $this->printer;
    }

    public function nextLine(float $height, float $nextColumnHeight = null)
    {
        $cursor = $this->getPrinter()->getCursor();
        $cursor = $cursor->withXCoordinate($this->activeColumn->getStart()->getXCoordinate());

        if (!$this->activeColumn->hasVerticalSpaceFor($cursor, $height)) {
            $this->nextColumn();
            $this->nextLine($nextColumnHeight ?? $height);
        } else {
            $cursor = $cursor->moveDown($height);
            $this->getPrinter()->setCursor($cursor);
        }
    }

    public function nextColumn()
    {
        $this->activeColumn = $this->layout->getNextColumn();
        $cursor = $this->activeColumn->getStart();
        $this->getPrinter()->setCursor($cursor);

        return $cursor;
    }

    /**
     * @var WordSizerRepository
     */
    private $wordSizerRepository;

    public function startParagraph(string $text)
    {
        $textStyle = $this->getPrinter()->getPrinter()->getTextStyle();
        $ascender = $textStyle->getAscender();
        $this->nextLine($ascender);

        $this->printPhrase($text);
    }

    public function endParagraph()
    {
        $textStyle = $this->getPrinter()->getPrinter()->getTextStyle();
        $descender = $textStyle->getDescender();
        $this->nextLine(-$descender, 0);
    }

    public function printPhrase(string $text)
    {
        $cursor = $this->getPrinter()->getCursor();

        $width = $this->activeColumn->getWidth();
        $availableWidth = $this->activeColumn->getAvailableWidth($cursor);

        $textStyle = $this->getPrinter()->getPrinter()->getTextStyle();
        $wordSizer = $this->wordSizerRepository->getWordSizer($textStyle->getFont());
        $columnBreaker = new ScaledColumnBreaker($textStyle, $wordSizer, $text);

        $leading = $textStyle->getLeading();
        $ascender = $textStyle->getAscender();

        // finish started line
        if ($availableWidth < $width) {
            [$line, $lineWidth] = $columnBreaker->nextLine($availableWidth);
            $this->printer->printText($line);

            if (!$columnBreaker->hasMoreLines()) {
                $this->moveRight($lineWidth);

                return;
            }

            $this->nextLine($leading, $ascender);

            $cursor = $this->getPrinter()->getCursor()->withXCoordinate($this->activeColumn->getStart()->getXCoordinate());
            $this->getPrinter()->setCursor($cursor);
        }

        // finish started column
        $maxLines = (int)$this->activeColumn->countSpaceFor($cursor, $leading) + 1;
        [$lines, $lineWidths] = $columnBreaker->nextColumn($width, $maxLines);
        $text = implode("\n", $lines);
        $this->printer->printText($text);

        $lastLineWidth = $lineWidths[\count($lineWidths) - 1];
        $this->moveRightDown($lastLineWidth, (\count($lines) - 1) * $leading);

        // print remaining text
        while ($columnBreaker->hasMoreLines()) {
            $cursor = $this->nextColumn();
            $this->nextLine($ascender);

            $maxLines = (int)$this->activeColumn->countSpaceFor($cursor, $leading) + 1;
            [$lines, $lineWidths] = $columnBreaker->nextColumn($width, $maxLines);
            $text = implode("\n", $lines);
            $this->printer->printText($text);

            $lastLineWidth = $lineWidths[\count($lineWidths) - 1];
            $this->moveRightDown($lastLineWidth, (\count($lines) - 1) * $leading);
        }
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
        $cursor = $this->getPrinter()->getCursor();
        if (!$this->activeColumn->hasHorizontalSpaceFor($cursor, $width)) {
            $this->nextLine($height);
        }

        $print();

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

    public function moveRightDown(float $width, float $height)
    {
        $cursor = $this->getPrinter()->getCursor();
        $cursor = $cursor->moveRightDown($width, $height);
        $this->getPrinter()->setCursor($cursor);
    }
}
