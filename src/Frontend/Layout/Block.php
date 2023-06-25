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

use PdfGenerator\Frontend\Layout\Base\BaseBlock;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

class Block extends BaseBlock
{
    private BaseBlock $block;

    public function __construct(BaseBlock $block)
    {
        parent::__construct();
        $this->block = $block;
    }

    public function getBlock(): BaseBlock
    {
        return $this->block;
    }

    /**
     * @template T
     *
     * @param AbstractBlockVisitor<T> $visitor
     *
     * @return T
     */
    public function accept(AbstractBlockVisitor $visitor): mixed
    {
        return $visitor->visitBlock($this);
    }
}
