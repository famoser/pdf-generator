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

use PdfGenerator\IR\Printer\Line;
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

    public function printLine(Cursor $cursor, Line $line): Cursor
    {
        foreach ($line->getFragments() as $fragment) {
            $this->setTextStyle($fragment->getTextStyle());
            $cursor = $this->printText($cursor, $fragment->getText(), $fragment->getWidth());
        }

        return $cursor;
    }

    public function printText(Cursor $cursor, string $text, float $right, float $down = 0): Cursor
    {
        $position = Position::fromCursor($cursor);
        $page = $this->document->getPage($cursor->getPageIndex());
        $this->printer->printText($page, $position, $text);

        $cursor = $cursor->moveRightDown($right, $down);
        $this->cursor = $cursor;

        return $this->cursor;
    }

    public function printImage(Cursor $cursor, Image $image, float $width, float $height): Cursor
    {
        $position = Position::fromCursor($cursor);
        $page = $this->document->getPage($cursor->getPageIndex());
        $this->printer->printImage($page, $position, $image, $width, $height);

        $cursor = $cursor->moveRight($width);
        $this->cursor = $cursor;

        return $this->cursor;
    }

    public function printRectangle(Cursor $cursor, float $width, float $height): Cursor
    {
        $position = Position::fromCursor($cursor);
        $page = $this->document->getPage($cursor->getPageIndex());
        $this->printer->printRectangle($page, $position, $width, $height);

        $cursor = $cursor->moveRight($width);
        $this->cursor = $cursor;

        return $this->cursor;
    }

    /**
     * @param Line[] $lines
     */
    public function printLines(Cursor $cursor, array $lines, float $columnStartXCoordinate, float $firstLineIndent): Cursor
    {
        $lineCount = \count($lines);
        for ($i = 0; $i < $lineCount; ++$i) {
            $line = $lines[$i];

            // place cursor
            if ($i === 0) {
                $cursor = $cursor->moveDown($line->getAscender());
                $cursor = $cursor->withXCoordinate($columnStartXCoordinate + $firstLineIndent);
            } else {
                $cursor = $cursor->moveDown($line->getLeading());
                $cursor = $cursor->withXCoordinate($columnStartXCoordinate);
            }

            $cursor = $this->printLine($cursor, $line);

            if ($i + 1 === $lineCount) {
                $cursor = $cursor->moveDown(-$line->getDescender());
            }
        }

        $this->cursor = $cursor;

        return $cursor;
    }
}
