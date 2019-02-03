<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Base;

use PdfGenerator\Frontend\Layout\Supporting\PrintBuffer;
use PdfGenerator\Frontend\PdfDocument;
use PdfGenerator\Frontend\Transaction\PrintTransaction;
use PdfGenerator\IR\Cursor;

abstract class BaseColumnedLayout
{
    /**
     * @var PdfDocument
     */
    private $pdfDocument;

    /**
     * @var int
     */
    private $columnCount;

    /**
     * @var float
     */
    private $totalWidth;

    /**
     * @var float[]
     */
    private $columnWidths;

    /**
     * @var float
     */
    private $columnGutter;

    /**
     * @var Cursor[]
     */
    private $columnCursors;

    /**
     * @var PrintBuffer
     */
    private $printBuffer;

    /**
     * ColumnLayout constructor.
     *
     * @param PdfDocument $pdfDocument
     * @param float $columnGutter
     * @param float $totalWidth
     * @param float[] $widths
     */
    protected function __construct(PdfDocument $pdfDocument, float $columnGutter, float $totalWidth, array $widths)
    {
        $this->pdfDocument = $pdfDocument;
        $this->columnCount = \count($widths);
        $this->columnGutter = $columnGutter;
        $this->totalWidth = $totalWidth;
        $this->columnWidths = $widths;

        $cursor = $pdfDocument->getCursor();
        $nextXStart = $cursor->getXCoordinate();
        $currentColumn = 0;
        do {
            $this->columnCursors[$currentColumn] = $cursor->setX($nextXStart);
            $nextXStart += $this->columnWidths[$currentColumn] + $this->columnGutter;
        } while (++$currentColumn < $this->columnCount);

        $this->printBuffer = new PrintBuffer($this->pdfDocument, $totalWidth);
    }

    /**
     * will end the columned layout.
     */
    public function getTransaction()
    {
        return self::createTransaction($this, $this->printBuffer, $this->pdfDocument, $this->totalWidth);
    }

    /**
     * @return PrintBuffer
     */
    protected function getPrintBuffer(): PrintBuffer
    {
        return $this->printBuffer;
    }

    /**
     * @return float[]
     */
    protected function getColumnWidths(): array
    {
        return $this->columnWidths;
    }

    /**
     * @return int
     */
    protected function getColumnCount(): int
    {
        return $this->columnCount;
    }

    /**
     * @return Cursor[]
     */
    protected function getColumnCursors(): array
    {
        return $this->columnCursors;
    }

    /**
     * ensures the next printed elements are printed in the specified column
     * will throw an exception if the column region does not exist.
     *
     * @param int $currentColumn
     * @param int $nextColumn
     */
    protected function switchColumns(int $currentColumn, int $nextColumn)
    {
        // save current cursor
        $this->columnCursors[$currentColumn] = $this->pdfDocument->getCursor();

        // set new cursor
        $this->pdfDocument->setCursor($this->columnCursors[$nextColumn]);
    }

    /**
     * @param int $column
     */
    protected function setColumnCursorFromDocument(int $column)
    {
        // save current cursor
        $this->columnCursors[$column] = $this->pdfDocument->getCursor();
    }

    /**
     * @param BaseColumnedLayout $columnedLayout
     * @param PrintBuffer $printBuffer
     * @param PdfDocument $pdfDocumentTransaction
     * @param float $width
     *
     * @return PrintTransaction
     */
    private static function createTransaction(self $columnedLayout, PrintBuffer $printBuffer, PdfDocument $pdfDocumentTransaction, float $width)
    {
        $printBuffer = PrintBuffer::createFromExisting($printBuffer);

        $printBuffer->addPrintable(function (PdfDocument $pdfDocument) use ($columnedLayout) {
            // go to lowest column after printing stopped
            $lowestCursor = $columnedLayout->columnCursors[0];
            for ($i = 1; $i < $columnedLayout->columnCount; ++$i) {
                $other = $columnedLayout->columnCursors[$i];
                if ($other->isBiggerThan($lowestCursor)) {
                    $lowestCursor = $other;
                }
            }

            $pdfDocument->setCursor($lowestCursor->setX($columnedLayout->columnCursors[0]->getXCoordinate()));
        });

        return new PrintTransaction($pdfDocumentTransaction, $width, $printBuffer->flushBufferClosure());
    }
}
