<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\LayoutEngine;

use Famoser\PdfGenerator\Frontend\Layout\Block;
use Famoser\PdfGenerator\Frontend\Layout\ContentBlock;
use Famoser\PdfGenerator\Frontend\Layout\Flow;
use Famoser\PdfGenerator\Frontend\Layout\Grid;
use Famoser\PdfGenerator\Frontend\Layout\Table;

/**
 * @template T
 */
interface BlockVisitorInterface
{
    /**
     * @return T
     */
    public function visitContentBlock(ContentBlock $contentBlock);

    /**
     * @return T
     */
    public function visitBlock(Block $block);

    /**
     * @return T
     */
    public function visitFlow(Flow $flow);

    /**
     * @return T
     */
    public function visitGrid(Grid $grid);

    /**
     * @return T
     */
    public function visitTable(Table $table);
}
