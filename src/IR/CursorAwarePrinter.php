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

use PdfGenerator\IR\Printer\StyleGetSetTrait;
use PdfGenerator\IR\Structure\Document;
use PdfGenerator\IR\Structure\Document\Image;
use PdfGenerator\IR\Structure\Document\Page\Content\Common\Position;

class CursorAwarePrinter
{
    use StyleGetSetTrait;

    /**
     * @var Cursor
     */
    protected $cursor;

    /**
     * @var Document
     */
    private $document;

    /**
     * @var Printer
     */
    private $printer;

    public function __construct(Document $document)
    {
        $this->document = $document;
        $this->printer = new Printer($document);
        $this->cursor = new Cursor(0, 0, 0);
    }

    public function getPrinter(): Printer
    {
        return $this->printer;
    }

    public function printText(string $text)
    {
        $position = Position::fromCursor($this->cursor);
        $page = $this->document->getPage($this->cursor->getPageIndex());
        $this->printer->printText($page, $position, $text);
    }

    public function printImage(Image $image, float $width, float $height)
    {
        $position = Position::fromCursor($this->cursor);
        $page = $this->document->getPage($this->cursor->getPageIndex());
        $this->printer->printImage($page, $position, $image, $width, $height);
    }

    public function printRectangle(float $width, float $height)
    {
        $position = Position::fromCursor($this->cursor);
        $page = $this->document->getPage($this->cursor->getPageIndex());
        $this->printer->printRectangle($page, $position, $width, $height);
    }
}
