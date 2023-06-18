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

use PdfGenerator\Frontend\Layout\Base\BaseBlock;
use PdfGenerator\Frontend\Layout\Block;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;
use PdfGenerator\Frontend\LayoutEngine\Measure\Measurer\FlowMeasurer;

/**
 * Measurements allow the layout engine to plan the layout. It contains:
 * - minimal space required to make progress
 * - average space required to fully print the content.
 *
 * @implements AbstractBlockVisitor<Measurement>
 */
class MeasurementVisitor extends AbstractBlockVisitor
{
    public function __construct(private readonly ?float $maxWidth = null, private readonly ?float $maxHeight = null)
    {
    }

    public function visitBlock(Block $block): Measurement
    {
        $contentMeasurement = $block->getBlock()->accept($this);

        return $this->measureBlock($block, $contentMeasurement);
    }

    public function visitFlow(Flow $flow): Measurement
    {
        $measurer = new FlowMeasurer($this->maxWidth, $this->maxHeight);
        $contentMeasurement = $measurer->measure($flow->getBlocks(), $flow->getDirection(), $flow->getDimensions(), $flow->getGap());

        return $this->measureBlock($flow, $contentMeasurement);
    }

    private function measureBlock(BaseBlock $block, Measurement $contentMeasurement): Measurement
    {
        $widthPadding = $block->getPadding()[0] + $block->getPadding()[2];
        $heightPadding = $block->getPadding()[1] + $block->getPadding()[3];
        $minContentHeight = $block->getHeight() ?? $contentMeasurement->getMinHeight() + $widthPadding;
        $minContentWidth = $block->getWidth() ?? $contentMeasurement->getMinWidth() + $heightPadding;

        $widthMargin = $block->getMargin()[0] + $block->getMargin()[2];
        $heightMargin = $block->getMargin()[1] + $block->getMargin()[3];
        $minHeight = $minContentHeight + $widthMargin;
        $minWidth = $minContentWidth + $heightMargin;

        // assumes blocks are more or less quadratic. should be OK for the approximate weight number
        $approximateDimension = sqrt($contentMeasurement->getWeight());
        $approximateWidth = $approximateDimension + $widthPadding + $widthMargin;
        $approximateHeight = $approximateDimension + $heightPadding + $heightMargin;
        $weight = $approximateWidth * $approximateHeight;

        return new Measurement($weight, $minWidth, $minHeight);
    }
}
