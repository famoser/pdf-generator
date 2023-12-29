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
        if (0 === count($grid->getRows())) {
            return [];
        }

        $columnSizes = $this->getColumnSizes($grid);
        $distributeWidth = $this->getDistributeWidth($columnSizes, $grid);

        /** @var BlockAllocation[][] $blockAllocationsPerColumn */
        $blockAllocationsPerColumn = [];
        $widths = [];
        foreach ($columnSizes as $columnIndex => $columnSize) {
            if (ColumnSize::MINIMAL !== $columnSize) {
                continue;
            }

            $usedColumnWidth = 0;
            $blockAllocationsPerColumn[$columnIndex] = $this->allocateMinColumn($grid, $columnIndex, $distributeWidth, $usedColumnWidth);
            $widths[$columnIndex] = $usedColumnWidth;
            $distributeWidth -= $usedColumnWidth;
        }

        $heights = [];
        foreach ($blockAllocationsPerColumn as $column => $blockAllocations) {
            foreach ($blockAllocations as $row => $blockAllocation) {
                $heights[$row] = isset($heights[$row]) ? max($heights[$row], $blockAllocation->getHeight()) : $blockAllocation->getHeight();
            }
        }

        /** @var BlockAllocation[] $allocatedBlocks */
        $allocatedBlocks = [];
        $overflowRows = $grid->getRows();
        $usedHeight = 0;
        foreach ($heights as $row => $height) {
            $progressMade = count($allocatedBlocks) > 0;
            $overflow = $usedHeight + $height > $this->height;
            if ($overflow && $progressMade) {
                $usedHeight = $grid->getPerpendicularGap();
                break;
            }

            array_shift($overflowRows);

            $currentWidth = 0;
            foreach ($blockAllocationsPerColumn as $column => $blockAllocations) {
                if (isset($blockAllocations[$row])) {
                    $blockAllocation = BlockAllocation::shift($blockAllocations[$row], $currentWidth, $usedHeight);

                    $allocatedBlocks[] = $blockAllocation;
                }

                $currentWidth += $widths[$column] + $grid->getGap();
            }

            $usedWidth = max($usedWidth, $currentWidth - $grid->getGap());
            $usedHeight += $height + $grid->getPerpendicularGap();
        }

        return $allocatedBlocks;
    }

    /**
     * @return BlockAllocation[][]
     */
    private function allocateMinColumn(Grid $grid, int $columnIndex, float $availableWidth, float &$usedWidth): array
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
            $progressMade = $rowIndex > 0;
            $overflow = $blockAllocation->getOverflow() || $blockAllocation->getHeight() > $availableHeight;
            if ($progressMade && $overflow) {
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
