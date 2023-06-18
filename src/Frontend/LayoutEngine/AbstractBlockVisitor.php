<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\LayoutEngine;

use PdfGenerator\Frontend\Layout\Block;
use PdfGenerator\Frontend\Layout\Content;
use PdfGenerator\Frontend\Layout\Flow;
use PdfGenerator\Frontend\Layout\Grid;
use PdfGenerator\Frontend\Layout\Table;

/**
 * @template T
 */
abstract class AbstractBlockVisitor
{
    /**
     * @return T
     */
    public function visitBlock(Block $block): mixed
    {
    }

    /**
     * @return T
     */
    public function visitFlow(Flow $flow): mixed
    {
    }

    /**
     * @return T
     */
    public function visitGrid(Grid $grid): mixed
    {
    }

    /**
     * @return T
     */
    public function visitTable(Table $table): mixed
    {
    }

    /**
     * @return T
     */
    public function visitParagraph(Content\Paragraph $param): mixed
    {
    }

    /**
     * @return T
     */
    public function visitRectangle(Content\Rectangle $rectangle): mixed
    {
    }

    /**
     * @return T
     */
    public function visitSpacer(Content\Spacer $param): mixed
    {
    }

    /**
     * @return T
     */
    public function visitImagePlacement(Content\ImagePlacement $param): mixed
    {
    }
}
