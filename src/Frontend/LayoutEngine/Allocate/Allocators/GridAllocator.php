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
use PdfGenerator\Frontend\LayoutEngine\Allocate\ContentAllocation;
use PdfGenerator\Frontend\LayoutEngine\Measure\BlockMeasurementVisitor;

readonly class GridAllocator
{
    public function __construct(private float $width, private float $height)
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

        $columnSizes = $grid->getNormalizedColumnSizes();

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
        foreach ($heights as $rowIndex => $height) {
            $progressMade = \count($allocatedBlocks) > 0;
            $overflow = $usedHeight + $height > $this->height;
            if ($overflow && $progressMade) {
                break;
            }

            array_shift($overflowRows);

            $currentWidth = 0;
            /** @var BlockAllocation[] $currentAllocatedBlocks */
            $currentAllocatedBlocks = [];
            foreach (array_keys($columnSizes) as $columnIndex) {
                if (isset($blockAllocationsPerColumn[$columnIndex][$rowIndex])) {
                    $blockAllocation = BlockAllocation::shift($blockAllocationsPerColumn[$columnIndex][$rowIndex], $currentWidth, $usedHeight);

                    $currentAllocatedBlocks[] = $blockAllocation;
                }

                $currentWidth += $widths[$columnIndex] + $grid->getGap();
            }

            $width = $currentWidth - $grid->getGap();
            $row = $grid->getRows()[$rowIndex];
            if ($row->getStyle() && $row->getStyle()->hasImpact()) {
                $background = ContentAllocation::createFromBlockStyle($width, $height, $row->getStyle());
                $backgroundAllocation = new BlockAllocation(0, $usedHeight, $width, $height, [], [$background]);
                array_unshift($currentAllocatedBlocks, $backgroundAllocation);
            }

            $allocatedBlocks = array_merge($allocatedBlocks, $currentAllocatedBlocks);
            $usedWidth = max($usedWidth, $width);
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
            } elseif (ColumnSize::isUnit($columnSize)) {
                $units = ColumnSize::parseUnit($columnSize);
                $unitsPerColumn[$columnIndex] = $units;
                $totalUnits += $units;
                $totalUnitsColumnSize += $optimalColumnWidth;
            } else {
                assert(false, 'ColumnSize '.$columnSize.' unknown.');
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
}
