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

use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

class Block extends AbstractBlock
{
    public function __construct(private AbstractBlock $block)
    {
        parent::__construct();
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function cloneWithBlock(AbstractBlock $block): self
    {
        $clone = clone $this;
        $clone->block = $block;

        return $clone;
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
