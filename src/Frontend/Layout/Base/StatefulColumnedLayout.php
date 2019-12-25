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

use PdfGenerator\Frontend\Document;

abstract class StatefulColumnedLayout extends BaseColumnedLayout
{
    /**
     * @var int
     */
    private $cursorPositionColumn = 0;

    /**
     * @var int
     */
    private $chosenColumn = 0;

    /**
     * ColumnLayout constructor.
     *
     * @param float[] $widths
     */
    protected function __construct(Document $pdfDocument, float $columnGutter, float $totalWidth, array $widths)
    {
        parent::__construct($pdfDocument, $columnGutter, $totalWidth, $widths);
    }

    /**
     * ensures the next printed elements are printed in the specified column
     * will throw an exception if the column region does not exist.
     *
     * @throws \Exception
     */
    public function setColumn(int $column)
    {
        if ($column >= $this->getColumnCount()) {
            throw new \Exception('column must be smaller than the column count');
        }

        $this->chosenColumn = $column;
    }

    /**
     * register a callable which prints to the pdf document
     * The position of the cursor at the time the callable is invoked is decided by the layout
     * ensure the cursor is below the printed content after the callable is finished to not mess up the layout.
     *
     * @param callable $callable takes a PdfDocument as first argument and the width as second
     *
     * @throws \Exception
     */
    public function registerPrintable(callable $callable)
    {
        $chosenColumn = $this->chosenColumn;

        $prepareArguments = function () use ($chosenColumn) {
            if ($this->cursorPositionColumn !== $chosenColumn) {
                $this->switchColumns($this->cursorPositionColumn, $chosenColumn);
                $this->cursorPositionColumn = $chosenColumn;
            }

            return [$this->getColumnWidths()[$this->cursorPositionColumn]];
        };

        $this->getPrintBuffer()->addPrintable($callable, $prepareArguments);
        $this->getPrintBuffer()->addPrintable(function () {
            $this->setColumnCursorFromDocument($this->cursorPositionColumn);
        });
    }
}
