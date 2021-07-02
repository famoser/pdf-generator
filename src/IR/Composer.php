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
use PdfGenerator\IR\Structure\Document\Page\Content\Common\Position;

class Composer
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

    /**
     * @var int[]
     */
    private $margin = [25, 25, 25, 25];

    public function __construct(Document $document)
    {
        $this->document = $document;
        $this->printer = new Printer($document);
        $this->cursor = new Cursor(0, 0, 0);
        $this->resetCursor();
    }

    public function getCursor(): Cursor
    {
        return $this->cursor;
    }

    public function getPrinter(): Printer
    {
        return $this->printer;
    }

    private function getPage(): Page
    {
        $pageCount = \count($this->document->getPages());
        $missingPages = $this->cursor->getPage() - $pageCount + 1;
        if ($missingPages > 0) {
            $size = $pageCount > 0 ? $this->document->getPages()[$pageCount - 1]->getSize() : null;
            for ($i = 0; $i < $missingPages; ++$i) {
                $this->document->addPage(new Page($pageCount + $i, $size));
            }
        }

        return $this->document->getPages()[$this->cursor->getPage()];
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

    public function moveCursor(float $down, float $right = 0)
    {
        $newRight = $this->cursor->getXCoordinate() + $right;
        $this->cursor->setX($newRight);

        $newBottom = $this->cursor->getYCoordinate() - $down;
        $this->cursor->setY($newBottom);
        if ($this->cursor->getYCoordinate() < $this->margin[2]) {
            $this->moveCursorToNextPage();
            $this->moveCursor($down);
        }
    }

    public function moveCursorToNextPage()
    {
        $this->cursor->setPage($this->cursor->getPage() + 1);
        $this->resetCursor(self::CURSOR_POSITION_TOP);
    }

    const CURSOR_POSITION_LEFT = 1;
    const CURSOR_POSITION_TOP = 2;

    public function resetCursor($cursorPosition = self::CURSOR_POSITION_LEFT | self::CURSOR_POSITION_TOP)
    {
        if ($cursorPosition & self::CURSOR_POSITION_LEFT) {
            $this->cursor->setX($this->margin[3]);
        }
        if ($cursorPosition & self::CURSOR_POSITION_TOP) {
            $startY = $this->getPage()->getSize()[1] - $this->margin[0];
            $this->cursor->setY($startY);
        }
    }

    public function printPhrase(string $text)
    {
        $words = explode(' ', $text);

        $position = Position::fromCursor($this->cursor);
        $page = $this->getPage();
        $this->printer->printText($page, $position, $text);
    }

    public function printImage(Image $image, float $width, float $height)
    {
        $this->moveCursor($height);

        $position = Position::fromCursor($this->cursor);
        $page = $this->getPage();
        $this->printer->printImage($page, $position, $image, $width, $height);
    }

    public function printRectangle(float $width, float $height)
    {
        $this->moveCursor($height);

        $position = Position::fromCursor($this->cursor);
        $page = $this->getPage();
        $this->printer->printRectangle($page, $position, $width, $height);
    }
}
