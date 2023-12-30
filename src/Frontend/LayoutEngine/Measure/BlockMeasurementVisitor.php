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

use PdfGenerator\Frontend\Layout\AbstractBlock;
use PdfGenerator\Frontend\Layout\Block;
use PdfGenerator\Frontend\Layout\ContentBlock;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;
use PdfGenerator\Frontend\LayoutEngine\Measure\Measurer\FlowMeasurer;

/**
 * Measurements allow the layout engine to plan the layout. It contains:
 * - minimal space required to make progress
 * - expected space required to fully print the content (given as-of-yet possibly unknown height/width).
 *
 * @implements AbstractBlockVisitor<Measurement>
 */
class BlockMeasurementVisitor extends AbstractBlockVisitor
{
    public function __construct()
    {
    }

    public function visitContentBlock(ContentBlock $contentBlock): Measurement
    {
        $contentMeasurementVisitor = new ContentMeasurementVisitor();
        $contentMeasurement = $contentBlock->getContent()->accept($contentMeasurementVisitor);

        return $this->measureBlock($contentBlock, $contentMeasurement);
    }

    public function visitBlock(Block $block): Measurement
    {
        $contentMeasurement = $block->getBlock()->accept($this);

        return $this->measureBlock($block, $contentMeasurement);
    }

    public function visitFlow(Flow $flow): Measurement
    {
        $measurer = new FlowMeasurer();
        $contentMeasurement = $measurer->measure($flow->getBlocks(), $flow->getDirection(), $flow->getGap());

        return $this->measureBlock($flow, $contentMeasurement);
    }

    private function measureBlock(AbstractBlock $block, Measurement $contentMeasurement): Measurement
    {
        $minHeight = $block->getHeight() ?? $contentMeasurement->getMinHeight() + $block->getXSpace();
        $minWidth = $block->getWidth() ?? $contentMeasurement->getMinWidth() + $block->getYSpace();

        // assumes blocks are more or less quadratic. should be OK for the approximate weight number
        $approximateDimension = sqrt($contentMeasurement->getWeight());
        $approximateWidth = ($block->getHeight() ?? $approximateDimension) + $block->getXSpace();
        $approximateHeight = ($block->getWidth() ?? $approximateDimension) + $block->getYSpace();
        $weight = $approximateWidth * $approximateHeight;

        return new Measurement($weight, $minWidth, $minHeight);
    }
}
