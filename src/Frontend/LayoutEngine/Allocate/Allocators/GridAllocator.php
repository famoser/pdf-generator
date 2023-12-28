<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators;

use PdfGenerator\Frontend\Layout\Grid;
use PdfGenerator\Frontend\Layout\Style\ColumnSize;
use PdfGenerator\Frontend\LayoutEngine\Allocate\BlockAllocation;
use PdfGenerator\Frontend\LayoutEngine\Allocate\BlockAllocationVisitor;

class GridAllocator
{
    public function __construct(private readonly float $width, private readonly float $height)
    {
    }

    /**
     * @return BlockAllocation[]
     */
    public function allocate(Grid $grid, array &$overflowRows = [], float &$usedWidth = 0, float &$usedHeight = 0): array
    {
        $columnSizes = $this->getColumnSizes($grid);
        $distributeWidth = $this->getDistributeWidth($columnSizes, $grid);

        $blockAllocations = [];
        foreach ($columnSizes as $columnIndex => $columnSize) {
            if (ColumnSize::MINIMAL !== $columnSize) {
                continue;
            }

            $blockAllocations[$columnIndex] = $this->allocateMinColumn($grid, $columnIndex, $distributeWidth, $usedColumnWidth);
            $distributeWidth -= $usedColumnWidth;
        }

        throw new \Exception('Not implemented yet');
    }

    /**
     * @return BlockAllocation[][]
     */
    private function allocateMinColumn(Grid $grid, int $columnIndex, float $availableWidth, float &$usedWidth = 0): array
    {
        $blockAllocations = [];
        $availableHeight = $this->height;
        foreach ($grid->getRows() as $rowIndex => $row) {
            if (!array_key_exists($columnIndex, $row->getColumns())) {
                continue;
            }

            $blockAllocator = new BlockAllocationVisitor($availableWidth, $availableHeight);
            /** @var BlockAllocation $blockAllocation */
            $blockAllocation = $row->getColumns()[$columnIndex]->accept($blockAllocator);

            // abort if not enough space, but progress made
            if ($rowIndex > 0 && $blockAllocation->getOverflow() || $blockAllocation->getHeight() > $availableHeight) {
                break;
            }

            $blockAllocations[$rowIndex] = $blockAllocation;
            $availableHeight -= $blockAllocation->getHeight();
            $usedWidth = max($usedWidth, $blockAllocation->getWidth());
        }

        return $blockAllocations;
    }

    /**
     * @return (float|ColumnSize)[]
     */
    private function getColumnSizes(Grid $grid): array
    {
        $maxColumn = max(...array_keys($grid->getColumnSizes()));
        foreach ($grid->getRows() as $row) {
            $maxColumn = max($maxColumn, ...array_keys($row->getColumns()));
        }

        $columnSizes = array_fill(0, $maxColumn + 1, ColumnSize::AUTO);
        foreach ($grid->getColumnSizes() as $index => $columnSize) {
            $columnSizes[$index] = $columnSize;
        }

        return $columnSizes;
    }

    /**
     * @param (ColumnSize|float)[] $columnSizes
     */
    public function getDistributeWidth(array $columnSizes, Grid $grid): float
    {
        $numberOfGaps = count($columnSizes) - 1;
        $distributeWidth = $this->width - $grid->getGap() * $numberOfGaps;
        foreach ($columnSizes as $columnSize) {
            if (is_numeric($columnSize)) {
                $distributeWidth -= $columnSize;
            }
        }

        return $distributeWidth;
    }
}
