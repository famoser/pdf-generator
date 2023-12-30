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
use PdfGenerator\Frontend\LayoutEngine\Measure\BlockMeasurementVisitor;

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
        if (0 === \count($grid->getRows())) {
            return [];
        }

        $columnSizes = $this->getColumnSizes($grid);

        $widths = [];
        $blockAllocationsPerColumn = $this->allocatedBlocksPerColumn($grid, $columnSizes, $widths);

        $heights = [];
        foreach ($blockAllocationsPerColumn as $blockAllocations) {
            foreach ($blockAllocations as $row => $blockAllocation) {
                $heights[$row] = isset($heights[$row]) ? max($heights[$row], $blockAllocation->getHeight()) : $blockAllocation->getHeight();
            }
        }

        /** @var BlockAllocation[] $allocatedBlocks */
        $allocatedBlocks = [];
        $overflowRows = $grid->getRows();
        $usedHeight = 0;
        foreach ($heights as $row => $height) {
            $progressMade = \count($allocatedBlocks) > 0;
            $overflow = $usedHeight + $height > $this->height;
            if ($overflow && $progressMade) {
                break;
            }

            array_shift($overflowRows);

            $currentWidth = 0;
            foreach (array_keys($columnSizes) as $columnIndex) {
                if (isset($blockAllocationsPerColumn[$columnIndex][$row])) {
                    $blockAllocation = BlockAllocation::shift($blockAllocationsPerColumn[$columnIndex][$row], $currentWidth, $usedHeight);

                    $allocatedBlocks[] = $blockAllocation;
                }

                $currentWidth += $widths[$columnIndex] + $grid->getGap();
            }

            $usedWidth = max($usedWidth, $currentWidth - $grid->getGap());
            $usedHeight += $height + $grid->getPerpendicularGap();
        }

        if ($usedHeight > 0) {
            $usedHeight -= $grid->getPerpendicularGap();
        }

        return $allocatedBlocks;
    }

    /**
     * @param (ColumnSize|string|numeric)[] $columnSizes
     *
     * @return BlockAllocation[][]
     */
    private function allocatedBlocksPerColumn(Grid $grid, array $columnSizes, array &$widths): array
    {
        $numberOfGaps = \count($columnSizes) - 1;
        $availableWidth = $this->width - $grid->getGap() * $numberOfGaps;

        // allocate fixed size or minimal size columns
        $blockAllocationsPerColumn = array_fill(0, \count($columnSizes), []);
        $widths = array_fill(0, \count($columnSizes), 0);
        $toBeMeasuredColumns = [];
        foreach ($columnSizes as $columnIndex => $columnSize) {
            if (ColumnSize::MINIMAL !== $columnSize && !\is_numeric($columnSize)) {
                $toBeMeasuredColumns[] = $columnIndex;
                continue;
            }

            $usedColumnWidth = 0;
            $blockAllocationsPerColumn[$columnIndex] = $this->allocateColumn($grid, $columnIndex, $availableWidth, $usedColumnWidth);

            $columnWidth = ColumnSize::MINIMAL === $columnSize ? $usedColumnWidth : $columnSize;
            $widths[$columnIndex] = $columnWidth;
            $availableWidth -= $columnWidth;
        }

        // measure auto and unit columns
        $expectedMaxWeight = ($this->width - $availableWidth) * $this->height;
        $totalWeight = 0;
        /** @var float[] $blockMeasurementsPerColumn */
        $weightPerColumn = array_fill(0, \count($columnSizes), 0);
        $measurer = new BlockMeasurementVisitor();
        foreach ($grid->getRows() as $row) {
            foreach ($toBeMeasuredColumns as $toBeMeasuredColumn) {
                if (!$row->tryGet($toBeMeasuredColumn)) {
                    continue;
                }

                $measurement = $row->tryGet($toBeMeasuredColumn)->accept($measurer);
                $weightPerColumn[$toBeMeasuredColumn] += $measurement->getWeight();
                $totalWeight += $measurement->getWeight();
            }

            if ($totalWeight > $expectedMaxWeight) {
                break;
            }
        }

        // distribute remaining weight to auto and unit columns
        /** @var float[] $unitsPerColumn */
        $unitsPerColumn = [];
        $totalUnits = 0;
        $totalUnitsColumnSize = 0;
        foreach ($toBeMeasuredColumns as $columnIndex) {
            $optimalColumnWidth = $totalWeight ? $weightPerColumn[$columnIndex] / $totalWeight * $availableWidth : 0;
            $columnSize = $columnSizes[$columnIndex];
            if (ColumnSize::AUTO === $columnSize) {
                $widths[$columnIndex] = $optimalColumnWidth;
                $usedWidth = 0;
                $blockAllocationsPerColumn[$columnIndex] = $this->allocateColumn($grid, $columnIndex, $optimalColumnWidth, $usedWidth);
            } elseif (ColumnSize::UNIT === $columnSize) {
                $unitsPerColumn[$columnIndex] = 1;
                ++$totalUnits;
                $totalUnitsColumnSize += $optimalColumnWidth;
            } elseif (str_ends_with($columnSize, ColumnSize::UNIT)) {
                $units = floatval($columnSize);
                $unitsPerColumn[$columnIndex] = $units;
                $totalUnits += $units;
                $totalUnitsColumnSize += $optimalColumnWidth;
            } else {
                $error = 'Unknown column size: '.$columnSize.'.';
                $explanation = 'Must be either a number, a ColumnSize, or a unit (number*, e.g. 2*).';
                assert(false, $error.' '.$explanation);
            }
        }

        // allocate unit columns
        if ($totalUnits > 0) {
            $columnSizePerUnit = $totalUnitsColumnSize / $totalUnits;
            foreach ($unitsPerColumn as $columnIndex => $units) {
                $width = $columnSizePerUnit * $units;
                $widths[$columnIndex] = $width;
                $usedWidth = 0;
                $blockAllocationsPerColumn[$columnIndex] = $this->allocateColumn($grid, $columnIndex, $width, $usedWidth);
            }
        }

        return $blockAllocationsPerColumn;
    }

    /**
     * @return BlockAllocation[][]
     */
    private function allocateColumn(Grid $grid, int $columnIndex, float $availableWidth, float &$usedWidth): array
    {
        $blockAllocations = [];
        $availableHeight = $this->height;
        foreach ($grid->getRows() as $rowIndex => $row) {
            if (!$row->tryGet($columnIndex)) {
                continue;
            }

            $blockAllocator = new BlockAllocationVisitor($availableWidth, $availableHeight);
            /** @var BlockAllocation $blockAllocation */
            $blockAllocation = $row->tryGet($columnIndex)->accept($blockAllocator);

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
}
