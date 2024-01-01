<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Layout;

use PdfGenerator\Frontend\Layout\Traits\BlockTrait;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

abstract class AbstractBlock
{
    use BlockTrait;

    /**
     * @template T
     *
     * @param AbstractBlockVisitor<T> $visitor
     *
     * @return T
     */
    abstract public function accept(AbstractBlockVisitor $visitor);
}
