<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine\Measure;

use PdfGenerator\Frontend\Layout\Block;
use PdfGenerator\Frontend\Layout\Content;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\Layout\Grid;
use PdfGenerator\Frontend\Layout\Table;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

/**
 * @implements AbstractBlockVisitor<Measurement>
 */
class MeasurementVisitor extends AbstractBlockVisitor
{
    public function __construct(private readonly ?float $maxWidth = null, private readonly ?float $maxHeight = null)
    {
    }

    /**
     * @return Measurement
     */
    public function visitBlock(Block $block): mixed
    {
        return $block->accept($this);
    }

    public function visitFlow(Flow $flow): mixed
    {
        // TODO: pass maxheight maxwidth to measurement visitor
        // TODO: sum area of children + area used by gaps
        $measurements = self::getMeasurements($flow->getBlocks(), $flow->getDirection(), $flow->getDimensions());
        $dimensionsArray = self::getDimensionsFromMeasurements($flow->getDirection(), $measurements);
        $maxDimensions = self::getMaxDimensions($flow->getDirection(), $this->maxWidth, $this->maxHeight);

        $dimensions = self::flowAlongDimension($dimensionsArray, $flow->getGap());

        return self::getMeasurementFromDimensions($flow->getDirection(), $dimensions);
    }

    /**
     * @param Block[]      $blocks
     * @param float[]|null $dimensions
     *
     * @return Measurement[]
     */
    private static function getMeasurements(array $blocks, string $direction, ?array $dimensions): array
    {
        /** @var Measurement[] $measurements */
        $measurements = [];
        for ($i = 0; $i < count($blocks); ++$i) {
            $block = $blocks[$i];
            $measurementVisitor = self::createMeasurementVisitorFromFlow($direction, $dimensions, $i);
            $measurements[] = $block->accept($measurementVisitor);
        }

        return $measurements;
    }

    /**
     * @param float[]|null $dimensions
     */
    private static function createMeasurementVisitorFromFlow(string $direction, ?array $dimensions, int $index): MeasurementVisitor
    {
        $dimension = $dimensions ? $dimensions[$index % count($dimensions)] : null;
        $maxWidth = Flow::DIRECTION_ROW === $direction ? $dimension : null;
        $maxHeight = Flow::DIRECTION_COLUMN === $direction ? $dimension : null;

        return new self($maxWidth, $maxHeight);
    }

    /**
     * @param Measurement[] $measurements
     *
     * @return float[][]
     */
    private static function getDimensionsFromMeasurements(string $direction, array $measurements): array
    {
        /** @var float[][] $result */
        $result = [];
        if (Flow::DIRECTION_ROW === $direction) {
            foreach ($measurements as $measurement) {
                $result[] = [$measurement->getWidth(), $measurement->getHeight()];
            }
        } else {
            foreach ($measurements as $measurement) {
                $result[] = [$measurement->getHeight(), $measurement->getWidth()];
            }
        }

        return $result;
    }

    /**
     * @return float[]
     */
    private static function getMaxDimensions(string $direction, ?float $maxWidth, ?float $maxHeight): array
    {
        if (Flow::DIRECTION_ROW === $direction) {
            return [$maxWidth, $maxHeight];
        } else {
            return [$maxHeight, $maxWidth];
        }
    }

    /**
     * @param float[][] $dimensionsArray
     *
     * @return float[]
     */
    private static function flowAlongDimension(array $dimensionsArray, ?float $gap = 0): array
    {
        $dimension = 0;
        $perpendicularDimension = 0;
        for ($i = 0; $i < count($dimensionsArray); ++$i) {
            $dimensions = $dimensionsArray[$i];

            if ($i > 0) {
                $dimension += $gap;
            }

            $dimension += $dimensions[0];
            $perpendicularDimension += $dimensions[1];
        }

        return [$dimension, $perpendicularDimension];
    }

    /**
     * @param float[] $dimensions
     */
    private static function getMeasurementFromDimensions(string $direction, array $dimensions, float $weight): Measurement
    {
        if (Flow::DIRECTION_ROW === $direction) {
            return new Measurement($dimensions[0], $dimensions[1], $weight);
        } else {
            return new Measurement($dimensions[1], $dimensions[0], $weight);
        }
    }

    public function visitGrid(Grid $grid): mixed
    {
        // TODO: Implement visitGrid() method.
    }

    public function visitTable(Table $table): mixed
    {
        // TODO: Implement visitTable() method.
    }

    public function visitContent(Content $content): mixed
    {
        // TODO: Implement visitContent() method.
    }
}
