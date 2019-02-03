<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout;

use DocumentGenerator\Layout\AutoColumnLayoutInterface;
use PdfGenerator\Frontend\Layout\Base\BaseColumnedLayout;
use PdfGenerator\Frontend\PdfDocument;

class AutoColumnLayout extends BaseColumnedLayout implements AutoColumnLayoutInterface
{
    /**
     * @var int
     */
    private $activeColumn = 0;

    /**
     * ColumnLayout constructor.
     *
     * @param PdfDocument $pdfDocument
     * @param int $columnCount
     * @param float $columnGutter
     * @param float $totalWidth
     */
    public function __construct(PdfDocument $pdfDocument, int $columnCount, float $columnGutter, float $totalWidth)
    {
        $gutterSpace = ($columnCount - 1) * $columnGutter;
        $columnWidth = (float)($totalWidth - $gutterSpace) / $columnCount;
        $columnWidths = [];
        for ($i = 0; $i < $columnCount; ++$i) {
            $columnWidths[] = $columnWidth;
        }

        parent::__construct($pdfDocument, $columnGutter, $totalWidth, $columnWidths);
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
        // set active cursor to highest cursor
        $prepareArguments = function () {
            // prepare variables
            $columnCursors = $this->getColumnCursors();
            $highestColumn = 0;
            $highestCursor = $columnCursors[0];
            $columnCount = $this->getColumnCount();

            // get highest cursor
            for ($i = 1; $i < $columnCount; ++$i) {
                $currentCursor = $columnCursors[$i];

                if ($highestCursor->isLowerOnPageThan($currentCursor)) {
                    $highestColumn = $i;
                    $highestCursor = $currentCursor;
                }
            }

            if ($this->activeColumn !== $highestColumn) {
                $this->switchColumns($this->activeColumn, $highestColumn);
                $this->activeColumn = $highestColumn;
            }

            return [$this->getColumnWidths()[$this->activeColumn]];
        };
        $this->getPrintBuffer()->addPrintable($callable, $prepareArguments);
        $this->getPrintBuffer()->addPrintable(function () {
            $this->setColumnCursorFromDocument($this->activeColumn);
        });
    }
}
