<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Layout\Traits;

use Famoser\PdfGenerator\Frontend\Layout\Style\ColumnSize;

trait ColumnSizesTrait
{
    /**
     * @var array<int, string|float|int|ColumnSize>
     */
    private array $columnSizes = [];

    /**
     * @return array<int, string|float|int|ColumnSize>
     */
    public function getColumnSizes(): array
    {
        return $this->columnSizes;
    }

    /**
     * returns all column sizes for all columns used in the grid.
     * if column size undefined for some column, defaults to AUTO.
     *
     * @return array<int, float|string|ColumnSize>
     */
    public function getNormalizedColumnSizes(): array
    {
        $maxColumn = max([0, ...array_keys($this->getColumnSizes())]);
        foreach ($this->getRows() as $row) {
            $maxColumn = max($maxColumn, ...array_keys($row->getColumns()));
        }

        $columnSizes = array_fill(0, $maxColumn + 1, ColumnSize::AUTO);
        foreach ($this->getColumnSizes() as $index => $columnSize) {
            /* @phpstan-ignore-next-line */
            if (is_int($columnSize)) {
                $columnSize = (float) $columnSize;
            }
            $columnSizes[$index] = $columnSize;
        }

        return $columnSizes;
    }
}
