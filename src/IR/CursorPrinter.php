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
use PdfGenerator\IR\Text\TextWriter\TextBlock;

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

    public function printTextBlock(Cursor $cursor, TextBlock $textBlock, float $lineStart, bool $continueParagraph = false): Cursor
    {
        $cursor = $cursor->moveRightDown($textBlock->getIndent(), $textBlock->getAscender());

        // baseline aligned
        foreach ($textBlock->getMeasuredPhrases() as $measuredPhrase) {
            $this->printer->setTextStyle($measuredPhrase->getTextStyle());

            $lines = $measuredPhrase->getLines();
            $lineWidths = $measuredPhrase->getLineWidths();
            $leading = $measuredPhrase->getTextStyle()->getLeading();

            if ($textBlock->getIndent() > 0 || $continueParagraph) {
                // as PDF does not know indent concept, whenever there is one need to print it as single line
                // happens the first time when the textblock has an indent, or when we are continuing a paragraph
                $line = array_shift($lines);
                $lineWidth = array_shift($lineWidths);

                $cursor = $this->printText($cursor, $line, $lineWidth);

                // if we have more lines, position the cursor on the next line
                if (\count($lines) > 0) {
                    $cursor = $cursor->moveDown($leading);
                    $cursor = $cursor->withXCoordinate($lineStart);
                } else {
                    continue;
                }
            }

            $text = implode("\n", $lines);
            $lastLineWidth = $lineWidths[\count($lines) - 1];
            $height = (\count($lines) - 1) * $leading;
            $cursor = $this->printText($cursor, $text, $lastLineWidth, $height);

            $continueParagraph = true;
        }

        $this->cursor = $cursor->moveDown(-$textBlock->getDescender());

        return $this->cursor;
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
}
