<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\LayoutEngine\Measure;

use Famoser\PdfGenerator\Frontend\Layout\AbstractElement;
use Famoser\PdfGenerator\Frontend\Layout\Block;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Flow;
use Famoser\PdfGenerator\Frontend\Layout\Grid;
use Famoser\PdfGenerator\Frontend\Layout\Table;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\LayoutEngine\ElementVisitorInterface;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Measure\Measurer\FlowMeasurer;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Measure\Measurer\GridMeasurer;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Measure\Measurer\TextMeasurer;

/**
 * Measurements allow the layout engine to plan the layout. It contains:
 * - minimal space required to make progress
 * - expected space required to fully print the content (given as-of-yet possibly unknown height/width).
 *
 * Measurements may not be exact; i.e. minWidth / minHeight may not correspond to what is then allocated.
 * They are still useful to layout; e.g. define auto column widths in tables.
 *
 * @implements  ElementVisitorInterface<Measurement>
 */
readonly class MeasurementVisitor implements ElementVisitorInterface
{
    public function visitContentBlock(ContentBlock $contentBlock): Measurement
    {
        $content = $contentBlock->getContent();
        $contentMeasurement = $content ? new Measurement($content->getWidth() * $content->getHeight(), $content->getWidth(), $content->getHeight()) : Measurement::zero();

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

    public function visitGrid(Grid $grid): Measurement
    {
        $measurer = new GridMeasurer();
        $contentMeasurement = $measurer->measure($grid->getRows(), $grid->getNormalizedColumnSizes(), $grid->getGap(), $grid->getPerpendicularGap());

        return $this->measureBlock($grid, $contentMeasurement);
    }

    public function visitTable(Table $table)
    {
        $measurer = new GridMeasurer();
        $contentMeasurement = $measurer->measure($table->getRows(), $table->getNormalizedColumnSizes(), 0, 0);

        return $this->measureBlock($table, $contentMeasurement);
    }

    public function visitText(Text $text): Measurement
    {
        $measurer = new TextMeasurer();
        $contentMeasurement = $measurer->measure($text->getSpans());

        return $this->measureBlock($text, $contentMeasurement);
    }

    private function measureBlock(AbstractElement $block, Measurement $contentMeasurement): Measurement
    {
        $minHeight = $block->getHeight() ?? $contentMeasurement->getMinHeight() + $block->getXSpace();
        $minWidth = $block->getWidth() ?? $contentMeasurement->getMinWidth() + $block->getYSpace();

        $approximateDimension = $contentMeasurement->calculateDimension();
        $approximateWidth = ($block->getHeight() ?? $approximateDimension) + $block->getXSpace();
        $approximateHeight = ($block->getWidth() ?? $approximateDimension) + $block->getYSpace();
        $weight = $approximateWidth * $approximateHeight;

        return new Measurement($weight, $minWidth, $minHeight);
    }
}
