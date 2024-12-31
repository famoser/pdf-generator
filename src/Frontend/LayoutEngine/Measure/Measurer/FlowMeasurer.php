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
use Famoser\PdfGenerator\Frontend\Layout\Style\FlowDirection;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Measure\MeasurementVisitor;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Measure\Measurement;

readonly class FlowMeasurer
{
    public function __construct()
    {
    }

    /**
     * @param AbstractElement[] $blocks
     */
    public function measure(array $blocks, FlowDirection $direction, float $gap): Measurement
    {
        $measurements = $this->getMeasurements($blocks);
        $weight = $this->getWeight($measurements, $direction, $gap);
        [$minWidth, $minHeight] = $this->getMinDimensions($measurements);

        return new Measurement($weight, $minWidth, $minHeight);
    }

    /**
     * @param AbstractElement[] $blocks
     *
     * @return Measurement[]
     */
    private function getMeasurements(array $blocks): array
    {
        /** @var Measurement[] $measurements */
        $measurements = [];
        for ($i = 0; $i < count($blocks); ++$i) {
            $block = $blocks[$i];

            $measurementVisitor = new MeasurementVisitor();
            $measurements[] = $block->accept($measurementVisitor);
        }

        return $measurements;
    }

    /**
     * @param Measurement[] $measurements
     */
    private function getWeight(array $measurements, FlowDirection $direction, float $gap): float
    {
        $weight = 0;
        for ($i = 0; $i < count($measurements); ++$i) {
            $measurement = $measurements[$i];
            $weight += $measurement->getWeight();

            if (0 === $i) {
                continue;
            }

            $previousMeasurement = $measurements[$i - 1];
            $dimension = FlowDirection::ROW === $direction ?
                $measurement->getMinHeight() + $previousMeasurement->getMinHeight() :
                $measurement->getMinWidth() + $previousMeasurement->getMinWidth();

            $weight += $dimension / 2 * $gap;
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
        $minWidth = $firstMeasurement?->getMinWidth() ?? 0;
        $minHeight = $firstMeasurement?->getMinHeight() ?? 0;

        return [$minWidth, $minHeight];
    }
}
