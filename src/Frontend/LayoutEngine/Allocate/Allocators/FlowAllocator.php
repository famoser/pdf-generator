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

use PdfGenerator\Frontend\Layout\Block;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\LayoutEngine\Allocate\Allocation;
use PdfGenerator\Frontend\LayoutEngine\Allocate\AllocationVisitor;
use PdfGenerator\Frontend\LayoutEngine\Measure\Measurement;

readonly class FlowAllocator
{
    public function __construct(private ?float $maxWidth, private ?float $maxHeight)
    {
    }

    public function allocate(Flow $flow): Allocation
    {
        $measurements = $this->getMeasurements($flow->getBlocks(), $flow->getDirection(), $flow->getDimensions());
        $weight = $this->getWeight($measurements, $flow);
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
        // TODO: how to prevent small blocks with irrelevant content to be located?

        $dimension = 0;
        $maxPerpendicularDimension = 0;

        /** @var Measurement[] $measurements */
        $measurements = [];
        for ($i = 0; $i < count($blocks); ++$i) {
            $block = $blocks[$i];

            $dimension = $dimensions ? $dimensions[$i % count($dimensions)] : null;
            $maxWidth = Flow::DIRECTION_ROW === $direction ? min($this->maxWidth, $dimension) : $this->maxWidth;
            $maxHeight = Flow::DIRECTION_COLUMN === $direction ? min($this->maxHeight, $dimension) : $this->maxHeight;
            $allocationVisitor = new AllocationVisitor($maxWidth, $maxHeight);

            $locatedBlock = $block->accept($allocationVisitor);
        }

        return $measurements;
    }

    public function getWeight(array $measurements, Flow $flow): float
    {
        $weight = 0;
        for ($i = 0; $i < count($measurements); ++$i) {
            $measurement = $measurements[$i];
            $weight += $measurement->getWeight();

            if (0 === $i) {
                continue;
            }

            $previousMeasurement = $measurements[$i - 1];
            $dimension = Flow::DIRECTION_ROW === $flow->getDirection() ?
                $measurement->getMinHeight() + $previousMeasurement->getMinHeight() :
                $measurement->getMinWidth() + $previousMeasurement->getMinWidth();

            $weight += (1.0 * $dimension) / 2 * $flow->getGap();
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
