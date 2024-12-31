<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\LayoutEngine\Measure\Measurer;

use Famoser\PdfGenerator\Frontend\Layout\AbstractElement;
use Famoser\PdfGenerator\Frontend\Layout\Parts\Row;
use Famoser\PdfGenerator\Frontend\Layout\Style\ColumnSize;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Measure\MeasurementVisitor;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Measure\Measurement;

readonly class GridMeasurer
{
    private MeasurementVisitor $measurementVisitor;

    public function __construct()
    {
        $this->measurementVisitor = new MeasurementVisitor();
    }

    /**
     * @param Row[]                       $rows
     * @param (ColumnSize|string|float)[] $columnSizes assumes array index 0...count(rows) are defined
     */
    public function measure(array $rows, array $columnSizes, float $gap, float $perpendicularGap): Measurement
    {
        if (0 === \count($rows)) {
            return Measurement::zero();
        }

        $firstRowMeasurements = $this->measureRow($rows[0]->getColumns());

        $minHeight = 0;
        $minWidth = 0;
        $minWidthPerUnit = 0;
        $totalUnits = 0;
        $totalWeight = 0;
        $totalDimensions = 0;
        foreach ($columnSizes as $columnIndex => $columnSize) {
            $measuredColumnFirstRow = $firstRowMeasurements[$columnIndex] ?? Measurement::zero();

            $minHeight = max($minHeight, $measuredColumnFirstRow->getMinHeight());

            $averageDimension = 0.0;
            $measuredColumn = $this->measureColumn($rows, $columnIndex, $measuredColumnFirstRow, $averageDimension);
            $totalDimensions += $averageDimension;
            $totalWeight += $measuredColumn->getWeight();

            if (is_numeric($columnSize)) {
                $minWidth += $columnSize;
            } elseif (ColumnSize::MINIMAL === $columnSize || ColumnSize::AUTO === $columnSize) {
                $minWidth += $measuredColumn->getMinWidth();
            } elseif (ColumnSize::isUnit($columnSize)) {
                $units = ColumnSize::parseUnit($columnSize);
                $totalUnits += $units;
                $widthPerUnit = $units > 0 ? $measuredColumn->getMinWidth() / $units : 0;
                $minWidthPerUnit = max($minWidthPerUnit, $widthPerUnit);
            } else {
                throw new \Exception('ColumnSize '.$columnSize.' unknown.');
            }
        }

        $minWidth += $minWidthPerUnit * $totalUnits;
        $minWidth += $gap * (count($columnSizes) - 1);

        $totalWeight += $totalDimensions * $gap * count($columnSizes);
        $totalWeight += $totalDimensions * $perpendicularGap * count($rows);

        return new Measurement($totalWeight, $minWidth, $minHeight);
    }

    /**
     * @param AbstractElement[] $columns
     *
     * @return Measurement[]
     */
    private function measureRow(array $columns): array
    {
        $columnMeasurements = [];
        foreach ($columns as $columnIndex => $column) {
            $columnMeasurements[$columnIndex] = $column->accept($this->measurementVisitor);
        }

        return $columnMeasurements;
    }

    /**
     * @param Row[] $rows
     */
    private function measureColumn(array $rows, int $columnIndex, Measurement $firstRowMeasurement, float &$averageRowDimension): Measurement
    {
        $measurement = $firstRowMeasurement;
        $totalRowDimension = 0.0;
        foreach ($rows as $rowIndex => $row) {
            if (0 === $rowIndex || !$row->tryGet($columnIndex)) {
                continue;
            }

            $rowMeasurement = $row->tryGet($columnIndex)->accept($this->measurementVisitor);
            $totalRowDimension += $rowMeasurement->calculateDimension();
            $measurement = new Measurement(
                $measurement->getWeight() + $rowMeasurement->getWeight(),
                \max($measurement->getMinWidth(), $rowMeasurement->getMinWidth()),
                \max($measurement->getMinHeight(), $rowMeasurement->getMinHeight())
            );
        }

        $averageRowDimension = $totalRowDimension / count($rows);

        return $measurement;
    }
}
