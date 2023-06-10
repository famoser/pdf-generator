<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\CursorPrinter\Buffer;

use PdfGenerator\Frontend\CursorPrinter\Buffer\RowBuffer\MeasuredColumn;
use PdfGenerator\Frontend\CursorPrinter\Buffer\RowBuffer\MeasuredRow;

class RowBuffer
{
    /**
     * @var MeasuredColumn[]
     */
    private array $columns = [];

    public function add(int $columnIndex, TextBuffer $buffer): void
    {
        $paragraph = $buffer->getMeasuredParagraph();

        $column = $this->getColumn($columnIndex);
        $column->addMeasuredParagraph($paragraph);
    }

    public function getRow(): MeasuredRow
    {
        $row = new MeasuredRow();

        foreach ($this->columns as $column) {
            $row->addMeasuredColumn($column);
        }

        return $row;
    }

    private function getColumn(int $columnIndex): MeasuredColumn
    {
        while (\count($this->columns) <= $columnIndex) {
            $this->columns[] = new MeasuredColumn();
        }

        return $this->columns[$columnIndex];
    }
}
