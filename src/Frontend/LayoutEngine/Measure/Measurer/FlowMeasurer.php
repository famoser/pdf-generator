<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Measure\Measurer;

use PdfGenerator\Frontend\Layout\Block;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\LayoutEngine\Measure\Measurement;
use PdfGenerator\Frontend\LayoutEngine\Measure\MeasurementVisitor;

readonly class FlowMeasurer
{
    public function __construct(private ?float $maxWidth, private ?float $maxHeight)
    {
    }

    public function measure(array $blocks, string $direction, ?array $dimensions, float $gap): Measurement
    {
        $measurements = $this->getMeasurements($blocks, $direction, $dimensions);
        $weight = $this->getWeight($measurements, $direction, $gap);
        [$minWidth, $minHeight] = $this->getMinDimensions($measurements);

        return new Measurement($weight, $minWidth, $minHeight);
    }

    /**
     * @param Block[]      $blocks
     * @param float[]|null $dimensions
     *
     * @return Measurement[]
     */
    private function getMeasurements(array $blocks, string $direction, ?array $dimensions): array
    {
        /** @var Measurement[] $measurements */
        $measurements = [];
        for ($i = 0; $i < count($blocks); ++$i) {
            $block = $blocks[$i];

            $dimension = $dimensions ? $dimensions[$i % count($dimensions)] : null;
            $maxWidth = Flow::DIRECTION_ROW === $direction ? min($this->maxWidth, $dimension) : $this->maxWidth;
            $maxHeight = Flow::DIRECTION_COLUMN === $direction ? min($this->maxHeight, $dimension) : $this->maxHeight;
            $measurementVisitor = new MeasurementVisitor($maxWidth, $maxHeight);

            $measurements[] = $block->accept($measurementVisitor);
        }

        return $measurements;
    }

    private function getWeight(array $measurements, string $direction, float $gap): float
    {
        $weight = 0;
        for ($i = 0; $i < count($measurements); ++$i) {
            $measurement = $measurements[$i];
            $weight += $measurement->getWeight();

            if (0 === $i) {
                continue;
            }

            $previousMeasurement = $measurements[$i - 1];
            $dimension = Flow::DIRECTION_ROW === $direction ?
                $measurement->getMinHeight() + $previousMeasurement->getMinHeight() :
                $measurement->getMinWidth() + $previousMeasurement->getMinWidth();

            $weight += (1.0 * $dimension) / 2 * $gap;
        }

        return $weight;
    }

    /**
     * @param Measurement[] $measurements
     *
     * @return float[]
     */
    private function getMinDimensions(array $measurements): array
    {
        $firstMeasurement = count($measurements) > 0 ? $measurements[0] : null;
        $minWidth = $firstMeasurement?->getMinWidth();
        $minHeight = $firstMeasurement?->getMinHeight();

        return [$minWidth, $minHeight];
    }
}
