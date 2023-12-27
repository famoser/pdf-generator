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
use PdfGenerator\Frontend\Layout\ContentBlock;
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
    abstract public function visitContentBlock(ContentBlock $contentBlock): mixed;

    /**
     * @return T
     */
    abstract public function visitBlock(Block $block): mixed;

    /**
     * @return T
     */
    abstract public function visitFlow(Flow $flow): mixed;

    /**
     * @return T
     */
    public function visitGrid(Grid $grid): mixed
    {
        throw new \Exception('not implemented');
    }

    /**
     * @return T
     */
    public function visitTable(Table $table): mixed
    {
        throw new \Exception('not implemented');
    }
}
