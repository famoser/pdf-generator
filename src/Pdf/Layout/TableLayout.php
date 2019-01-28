<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Pdf\Layout;

use PdfGenerator\Layout\Configuration\ColumnConfiguration;
use PdfGenerator\Layout\TableLayoutInterface;
use PdfGenerator\Layout\TableRowLayoutInterface;
use PdfGenerator\Pdf\PdfDocumentInterface;
use PdfGenerator\Pdf\Transaction\PrintTransaction;
use PdfGenerator\Transaction\TransactionInterface;

class TableLayout implements TableLayoutInterface
{
    /**
     * @var PdfDocumentInterface
     */
    private $pdfDocument;

    /**
     * @var float
     */
    private $width;

    /**
     * @var float
     */
    private $columnGutter;

    /**
     * @var float[]
     */
    private $columnWidths;

    /**
     * @var int
     */
    private $columnCount;

    /**
     * @var TableRowLayout[]
     */
    private $rows;

    /**
     * @var callable
     */
    private $onRowCommit;

    /**
     * @param PdfDocumentInterface $pdfDocument
     * @param float $width
     * @param float $columnGutter
     * @param ColumnConfiguration[] $columnConfiguration
     *
     * @throws \Exception
     */
    public function __construct(PdfDocumentInterface $pdfDocument, float $width, float $columnGutter, array $columnConfiguration)
    {
        $this->pdfDocument = $pdfDocument;
        $this->width = $width;
        $this->columnGutter = $columnGutter;

        $this->columnCount = \count($columnConfiguration);
        $this->columnWidths = $this->calculateColumnWidths($pdfDocument, $columnConfiguration, $this->width, $this->columnGutter, $this->columnCount);
    }

    /**
     * @param callable $callable
     */
    public function setOnRowCommit(callable $callable): void
    {
        $this->onRowCommit = $callable;
    }

    /**
     * @return TableRowLayoutInterface
     */
    public function startNewRow()
    {
        $layout = new TableRowLayout($this->pdfDocument, $this->columnGutter, $this->width, $this->columnWidths);
        $this->rows[] = $layout;

        return $layout;
    }

    /**
     * will produce a transaction with the to-be-printed document.
     *
     * @return TransactionInterface
     */
    public function getTransaction()
    {
        $onRowCommit = $this->onRowCommit;

        $flushRows = function () use ($onRowCommit) {
            foreach ($this->rows as $row) {
                $transaction = $row->getTransaction();
                if ($onRowCommit !== null) {
                    $onRowCommit($transaction);
                }
                $transaction->commit();
            }
        };

        return new PrintTransaction($this->pdfDocument, $this->width, $flushRows);
    }

    /**
     * @param PdfDocumentInterface $pdfDocument
     * @param ColumnConfiguration[] $columnConfiguration
     * @param float $width
     * @param float $columnGutter
     * @param int $columnCount
     *
     * @throws \Exception
     *
     * @return float[]
     */
    private static function calculateColumnWidths(PdfDocumentInterface $pdfDocument, array $columnConfiguration, float $width, float $columnGutter, int $columnCount)
    {
        $gutterSpace = (\count($columnConfiguration) - 1) * $columnGutter;
        $availableWidth = $width - $gutterSpace;

        $expandColumns = [];
        $widths = [];
        for ($i = 0; $i < $columnCount; ++$i) {
            $column = $columnConfiguration[$i];
            if ($column->getSizing() === ColumnConfiguration::SIZING_EXPAND) {
                $expandColumns[] = $i;
            } elseif ($column->getSizing() === ColumnConfiguration::SIZING_BY_TEXT) {
                $text = $column->getText();
                $width = $pdfDocument->calculateWidthOfText($text);

                $availableWidth -= $width;
                $widths[$i] = $width;
            } else {
                throw new \Exception('sizing mode ' . $column->getSizing() . ' not supported');
            }
        }

        // calculate expand widths
        $expandColumnsCount = \count($expandColumns);
        if ($expandColumnsCount > 0) {
            $expandColumnWidth = $availableWidth / $expandColumnsCount;
            foreach ($expandColumns as $expandColumn) {
                $widths[$expandColumn] = $expandColumnWidth;
            }
        }

        return $widths;
    }
}
