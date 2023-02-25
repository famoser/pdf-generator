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

use DocumentGenerator\Layout\Configuration\ColumnConfiguration;
use DocumentGenerator\Layout\TableLayoutInterface;
use DocumentGenerator\Layout\TableRowLayoutInterface;
use DocumentGenerator\Transaction\TransactionInterface;
use PdfGenerator\Frontend\Document;
use PdfGenerator\Frontend\Transaction\ComposedTransaction;

class TableLayout implements TableLayoutInterface
{
    /**
     * @var Document
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
     * @param ColumnConfiguration[] $columnConfiguration
     *
     * @throws \Exception
     */
    public function __construct(Document $pdfDocument, float $width, float $columnGutter, array $columnConfiguration)
    {
        $this->pdfDocument = $pdfDocument;
        $this->width = $width;
        $this->columnGutter = $columnGutter;

        $this->columnCount = \count($columnConfiguration);
        $this->columnWidths = $this->calculateColumnWidths($pdfDocument, $columnConfiguration, $this->width, $this->columnGutter, $this->columnCount);
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
        /** @var TransactionInterface[] $transactions */
        $transactions = [];
        foreach ($this->rows as $row) {
            $transactions[] = $row->getTransaction();
        }

        return new ComposedTransaction($this->pdfDocument, $transactions);
    }

    /**
     * @param ColumnConfiguration[] $columnConfiguration
     *
     * @return float[]
     *
     * @throws \Exception
     */
    private static function calculateColumnWidths(Document $pdfDocument, array $columnConfiguration, float $width, float $columnGutter, int $columnCount)
    {
        $gutterSpace = (\count($columnConfiguration) - 1) * $columnGutter;
        $availableWidth = $width - $gutterSpace;

        $expandColumns = [];
        $widths = [];
        for ($i = 0; $i < $columnCount; ++$i) {
            $column = $columnConfiguration[$i];
            if (ColumnConfiguration::SIZING_EXPAND === $column->getSizing()) {
                $expandColumns[] = $i;
            } elseif (ColumnConfiguration::SIZING_BY_TEXT === $column->getSizing()) {
                $text = $column->getText();
                $width = $pdfDocument->calculateWidthOfText($text);

                $availableWidth -= $width;
                $widths[$i] = $width;
            } else {
                throw new \Exception('sizing mode '.$column->getSizing().' not supported');
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
