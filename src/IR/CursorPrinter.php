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

class CursorPrinter
{
    use StyleGetSetTrait;

    /**
     * @var Cursor
     */
    private $cursor;

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

    public function getCursor(): Cursor
    {
        return $this->cursor;
    }

    public function setCursor(Cursor $cursor): void
    {
        $this->cursor = $cursor;
    }

    public function printText(Cursor $cursor, string $text)
    {
        $position = Position::fromCursor($cursor);
        $page = $this->document->getPage($cursor->getPageIndex());
        $this->printer->printText($page, $position, $text);

        $this->cursor = $cursor;
    }

    public function printImage(Cursor $cursor, Image $image, float $width, float $height)
    {
        $position = Position::fromCursor($cursor);
        $page = $this->document->getPage($cursor->getPageIndex());
        $this->printer->printImage($page, $position, $image, $width, $height);

        $this->cursor = $cursor;
    }

    public function printRectangle(Cursor $cursor, float $width, float $height)
    {
        $position = Position::fromCursor($cursor);
        $page = $this->document->getPage($cursor->getPageIndex());
        $this->printer->printRectangle($page, $position, $width, $height);

        $this->cursor = $cursor;
    }
}
