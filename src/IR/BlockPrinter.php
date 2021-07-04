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

use PdfGenerator\IR\Structure\Document;
use PdfGenerator\IR\Structure\Document\Image;
use PdfGenerator\IR\Structure\Document\Page;
use PdfGenerator\IR\Text\LineBreak\FontSizer\FontSizerRepository;
use PdfGenerator\IR\Text\LineBreak\LineBreaker;

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
     * @var int[]
     */
    private $margin = [25, 25, 25, 25];

    public function __construct(Document $document)
    {
        $this->document = $document;
        $this->printer = new FlowPrinter($document);
        $this->ensureMarginsRespected();

        $this->fontSizerRepository = new FontSizerRepository();
    }

    public function getPrinter(): FlowPrinter
    {
        return $this->printer;
    }

    public function setPageMargin(float $top, float $right = null, float $bottom = null, float $left = null)
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

    private function ensureMarginsRespected()
    {
        if ($this->getPrinter()->getCursor()->getXCoordinate() < $this->margin[3]) {
            $this->getPrinter()->setLeft($this->margin[3]);
        }

        if ($this->getPrinter()->getCursor()->getXCoordinate() < $this->margin[3]) {
            $this->getPrinter()->setLeft($this->margin[3]);
        }
        $this->getPrinter()->setTop($this->margin[0]);
        $this->getPrinter()->setTop($this->margin[0]);
    }

    /**
     * ensures a block of specified height has space within page margins.
     * advances page if it does not fit (only once; the block is placed on the next page no matter its size).
     */
    private function placeBlock(float $height, float $nextPageHeight = null)
    {
        $this->printer->moveDown($height);

        if ($this->printer->getCursor()->getYCoordinate() < $this->margin[2]) {
            if ($nextPageHeight === null) {
                $nextPageHeight = $height;
            }

            $this->printer->advancePage();
            $this->getPrinter()->setTop($this->margin[0] + $nextPageHeight);
        }
    }

    const CURSOR_POSITION_TOP = 1;
    const CURSOR_POSITION_LEFT = 2;
    const CURSOR_POSITION_TOP_LEFT = self::CURSOR_POSITION_TOP | self::CURSOR_POSITION_LEFT;

    public function resetCursor($cursorPosition = self::CURSOR_POSITION_TOP_LEFT)
    {
        if ($cursorPosition & self::CURSOR_POSITION_TOP) {
        }
    }

    /**
     * @var FontSizerRepository
     */
    private $fontSizerRepository;

    public function printParagraph(string $text)
    {
        $textStyle = $this->getPrinter()->getPrinter()->getTextStyle();
        $fontSizer = $this->fontSizerRepository->getFontSizer($textStyle);

        $lineBreaker = new LineBreaker($fontSizer, $text);
        $availableWidth = $this->margin[1] - $this->getPrinter()->getCursor()->getXCoordinate();
        $lineBreakerIterator = $lineBreaker->getIterator($availableWidth);

        // print first line
        [$words, $width] = $lineBreakerIterator->current();
        $this->printer->printText($words);
        $this->getPrinter()->moveRight($width);
        $lineBreakerIterator->next();

        // further lines
        $paragraphWidth = $this->margin[1] - $this->margin[3];
        $currentWordPosition = $lineBreakerIterator->key();
        $lineBreakerIterator = $lineBreaker->getIterator($paragraphWidth, $currentWordPosition);
        while ($lineBreakerIterator->valid()) {
            [$words, $width] = $lineBreakerIterator->current();

            $this->getPrinter()->moveDown($lineHeight);
            $this->getPrinter()->setLeft($this->margin[3]);

            $this->printer->printText($words);
            $this->getPrinter()->moveRight($width);

            $lineBreakerIterator->next();
        }
    }

    public function printImage(Image $image, float $width, float $height)
    {
        $this->printer->printImage($image, $width, $height);
    }

    public function printRectangle(float $width, float $height)
    {
        $this->printer->printRectangle($width, $height);
    }
}
