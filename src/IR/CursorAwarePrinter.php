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
use PdfGenerator\IR\Structure\Document\Page\Content\Common\Position;

class CursorAwarePrinter
{
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

    public function getCursor(): Cursor
    {
        return $this->cursor;
    }

    public function getPrinter(): Printer
    {
        return $this->printer;
    }

    public function setTop(float $top)
    {
        $page = $this->document->getOrCreatePage($this->cursor->getPage());
        $yCoordinate = $page->getSize()[0] - $top;
        $this->cursor = $this->cursor->withYCoordinate($yCoordinate);
    }

    public function setLeft(float $left)
    {
        $this->cursor = $this->cursor->withXCoordinate($left);
    }

    public function setPage(int $page)
    {
        $this->cursor = $this->cursor->withPage($page);
    }

    public function moveRight(float $right)
    {
        $newXCoordinate = $this->cursor->getXCoordinate() + $right;
        $this->cursor = $this->cursor->withXCoordinate($newXCoordinate);
    }

    public function moveDown(float $down)
    {
        $newYCoordinate = $this->cursor->getYCoordinate() - $down;
        $this->cursor = $this->cursor->withYCoordinate($newYCoordinate);
    }

    public function advancePage(int $nextPage = 1)
    {
        $newPage = $this->cursor->getPage() + $nextPage;
        $this->cursor = $this->cursor->withPage($newPage);
    }

    public function printText(string $text)
    {
        $position = Position::fromCursor($this->cursor);
        $page = $this->document->getOrCreatePage($this->cursor->getPage());
        $this->printer->printText($page, $position, $text);
    }

    public function printImage(Image $image, float $width, float $height)
    {
        $position = Position::fromCursor($this->cursor);
        $page = $this->document->getOrCreatePage($this->cursor->getPage());
        $this->printer->printImage($page, $position, $image, $width, $height);
    }

    public function printRectangle(float $width, float $height)
    {
        $position = Position::fromCursor($this->cursor);
        $page = $this->document->getOrCreatePage($this->cursor->getPage());
        $this->printer->printRectangle($page, $position, $width, $height);
    }
}
