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
use PdfGenerator\Frontend\LayoutEngine\Measure\Layout\FlowMeasuring;

/**
 * @implements AbstractBlockVisitor<Measurement>
 */
class MeasurementVisitor extends AbstractBlockVisitor
{
    public function __construct(private readonly ?float $maxWidth = null, private readonly ?float $maxHeight = null)
    {
    }

    public function visitBlock(Block $block): Measurement
    {
        return $block->accept($this);
    }

    public function visitFlow(Flow $flow): Measurement
    {
        $measuring = new FlowMeasuring($this->maxWidth, $this->maxHeight);

        return $measuring->measure($flow);
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
