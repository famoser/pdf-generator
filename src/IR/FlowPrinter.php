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
use PdfGenerator\IR\Text\LineBreak\FontSizer\FontSizerRepository;
use PdfGenerator\IR\Text\LineBreak\LineBreaker;

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
     * @var int[]
     */
    private $margin = [25, 25, 25, 25];

    public function __construct(Document $document)
    {
        $this->document = $document;
        $this->printer = new CursorAwarePrinter($document);

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
     * @var FontSizerRepository
     */
    private $fontSizerRepository;

    public function printText(string $text)
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

            $this->getPrinter()->moveDown($fontSizer->getBaselineToBaselineDistance());
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
