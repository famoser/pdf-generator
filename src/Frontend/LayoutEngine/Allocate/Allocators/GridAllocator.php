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
use PdfGenerator\Frontend\Layout\Parts\Row;
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
     * @param Row[] $overflowRows
     *
     * @return BlockAllocation[]
     */
    public function allocate(Grid $grid, array &$overflowRows = [], float &$usedWidth = 0, float &$usedHeight = 0): array
    {
        $columnSizes = $grid->getNormalizedColumnSizes();

        $widthsPerColumn = [];
        $availableWidth = $this->width - $grid->getGap() * (\count($columnSizes) - 1);
        $blockAllocationsPerColumn = static::allocatedBlocksPerColumn($grid->getRows(), $columnSizes, $availableWidth, $this->height, 1, $widthsPerColumn);

        return static::allocateRows($grid->getRows(), $blockAllocationsPerColumn, $widthsPerColumn, $this->height, $grid->getGap(), $grid->getPerpendicularGap(), 1, $overflowRows, $usedWidth, $usedHeight);
    }

    /**
     * @param Row[]                         $rows
     * @param array<int, BlockAllocation[]> $blockAllocationsPerColumn
     * @param array<int, float>             $widthsPerColumn
     * @param Row[]                         $overflowRows
     *
     * @return BlockAllocation[]
     */
    public static function allocateRows(array $rows, array $blockAllocationsPerColumn, array $widthsPerColumn, float $availableHeight, float $gap, float $perpendicularGap, int $minimalRowAllocations, array &$overflowRows, float &$usedWidth, float &$usedHeight): array
    {
        $heights = [];
        foreach ($blockAllocationsPerColumn as $blockAllocations) {
            foreach ($blockAllocations as $row => $blockAllocation) {
                $heights[$row] = isset($heights[$row]) ? max($heights[$row], $blockAllocation->getHeight()) : $blockAllocation->getHeight();
            }
        }

        /** @var BlockAllocation[] $allocatedBlocks */
        $allocatedBlocks = [];
        $overflowRows = $rows;
        $usedHeight = 0;
        foreach ($heights as $rowIndex => $height) {
            $progressMade = \count($allocatedBlocks) > 0;
            $overflow = $usedHeight + $height > $availableHeight;
            if ($overflow && $progressMade && $rowIndex >= $minimalRowAllocations) {
                break;
            }

            array_shift($overflowRows);

            $currentWidth = 0;
            /** @var BlockAllocation[] $currentAllocatedBlocks */
            $currentAllocatedBlocks = [];
            foreach ($blockAllocationsPerColumn as $columnIndex => $blockAllocations) {
                if (isset($blockAllocations[$rowIndex])) {
                    $blockAllocation = BlockAllocation::shift($blockAllocations[$rowIndex], $currentWidth, $usedHeight);

                    $currentAllocatedBlocks[] = $blockAllocation;
                }

                $currentWidth += $widthsPerColumn[$columnIndex] + $gap;
            }

            $width = $currentWidth - $gap;
            $row = $rows[$rowIndex];
            if ($row->getStyle() && $row->getStyle()->hasImpact()) {
                $background = ContentAllocation::createFromBlockStyle($width, $height, $row->getStyle());
                $backgroundAllocation = new BlockAllocation(0, $usedHeight, $width, $height, [], [$background]);
                array_unshift($currentAllocatedBlocks, $backgroundAllocation);
            }

            $allocatedBlocks = array_merge($allocatedBlocks, $currentAllocatedBlocks);
            $usedWidth = max($usedWidth, $width);
            $usedHeight += $height + $perpendicularGap;
        }

        if ($usedHeight > 0) {
            $usedHeight -= $perpendicularGap;
        }

        return $allocatedBlocks;
    }

    /**
     * @param Row[]                         $rows
     * @param (ColumnSize|string|numeric)[] $columnSizes
     * @param array<int, float>             $widthsPerColumn
     *
     * @return array<int, BlockAllocation[]>
     */
    public static function allocatedBlocksPerColumn(array $rows, array $columnSizes, float $availableWidth, float $availableHeight, int $minimalRowAllocations, array &$widthsPerColumn): array
    {
        $remainingWidth = $availableWidth;

        // allocate fixed size or minimal size columns
        $blockAllocationsPerColumn = array_fill(0, \count($columnSizes), []);
        $widthsPerColumn = array_fill(0, \count($columnSizes), 0);
        $toBeMeasuredColumns = [];
        foreach ($columnSizes as $columnIndex => $columnSize) {
            if (ColumnSize::MINIMAL !== $columnSize && !\is_numeric($columnSize)) {
                $toBeMeasuredColumns[] = $columnIndex;
                continue;
            }

            $usedColumnWidth = 0;
            $blockAllocationsPerColumn[$columnIndex] = static::allocateColumn($rows, $columnIndex, $remainingWidth, $availableHeight, $minimalRowAllocations, $usedColumnWidth);

            $columnWidth = ColumnSize::MINIMAL === $columnSize ? $usedColumnWidth : $columnSize;
            $widthsPerColumn[$columnIndex] = $columnWidth;
            $remainingWidth -= $columnWidth;
        }

        // measure auto and unit columns
        $expectedMaxWeight = $remainingWidth * $availableHeight;
        $totalWeight = 0;
        /** @var float[] $weightPerColumn */
        $weightPerColumn = array_fill(0, \count($columnSizes), 0);
        $measurer = new BlockMeasurementVisitor();
        foreach ($rows as $row) {
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
            $optimalColumnWidth = $totalWeight ? $weightPerColumn[$columnIndex] / $totalWeight * $remainingWidth : 0;
            $columnSize = $columnSizes[$columnIndex];
            if (ColumnSize::AUTO === $columnSize) {
                $widthsPerColumn[$columnIndex] = $optimalColumnWidth;
                $usedWidth = 0;
                $blockAllocationsPerColumn[$columnIndex] = static::allocateColumn($rows, $columnIndex, $optimalColumnWidth, $availableHeight, $minimalRowAllocations, $usedWidth);
            } elseif (ColumnSize::isUnit($columnSize)) {
                $units = ColumnSize::parseUnit($columnSize);
                $unitsPerColumn[$columnIndex] = $units;
                $totalUnits += $units;
                $totalUnitsColumnSize += $optimalColumnWidth;
            } else {
                throw new \Exception('ColumnSize '.$columnSize.' unknown.');
            }
        }

        // allocate unit columns
        if ($totalUnits > 0) {
            $columnSizePerUnit = $totalUnitsColumnSize / $totalUnits;
            foreach ($unitsPerColumn as $columnIndex => $units) {
                $width = $columnSizePerUnit * $units;
                $widthsPerColumn[$columnIndex] = $width;
                $usedWidth = 0;
                $blockAllocationsPerColumn[$columnIndex] = static::allocateColumn($rows, $columnIndex, $width, $availableHeight, $minimalRowAllocations, $usedWidth);
            }
        }

        ksort($blockAllocationsPerColumn);

        return $blockAllocationsPerColumn;
    }

    /**
     * @param Row[] $rows
     *
     * @return array<int,BlockAllocation>
     */
    public static function allocateColumn(array $rows, int $columnIndex, float $availableWidth, float $availableHeight, int $minimalRowAllocations, float &$usedWidth): array
    {
        $blockAllocations = [];
        $remainingHeight = $availableHeight;
        foreach ($rows as $rowIndex => $row) {
            if (!$row->tryGet($columnIndex)) {
                continue;
            }

            $blockAllocator = new BlockAllocationVisitor($availableWidth, $remainingHeight);
            $blockAllocation = $row->tryGet($columnIndex)->accept($blockAllocator);

            // abort if not enough space, but progress made
            $progressMade = $rowIndex > 0;
            $overflow = $blockAllocation->getOverflow() || $blockAllocation->getHeight() > $remainingHeight;
            if ($progressMade && $overflow && $rowIndex >= $minimalRowAllocations) {
                break;
            }

            $blockAllocations[$rowIndex] = $blockAllocation;
            $remainingHeight -= $blockAllocation->getHeight();
            $usedWidth = max($usedWidth, $blockAllocation->getWidth());
        }

        return $blockAllocations;
    }
}
