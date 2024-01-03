<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout\Traits;

use PdfGenerator\Frontend\Layout\Style\ColumnSize;

trait ColumnSizesTrait
{
    private array $columnSizes = [];

    /**
     * @return (string|float|ColumnSize)[]
     */
    public function getColumnSizes(): array
    {
        return $this->columnSizes;
    }

    /**
     * returns all column sizes for all columns used in the grid.
     * if column size undefined for some column, defaults to AUTO.
     *
     * @return (float|string|ColumnSize)[]
     */
    public function getNormalizedColumnSizes(): array
    {
        $maxColumn = max([0, ...array_keys($this->getColumnSizes())]);
        foreach ($this->getRows() as $row) {
            $maxColumn = max($maxColumn, ...array_keys($row->getColumns()));
        }

        $columnSizes = array_fill(0, $maxColumn + 1, ColumnSize::AUTO);
        foreach ($this->getColumnSizes() as $index => $columnSize) {
            $columnSizes[$index] = $columnSize;
        }

        return $columnSizes;
    }
}
