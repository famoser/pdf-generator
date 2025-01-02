<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Layout;

use Famoser\PdfGenerator\Frontend\LayoutEngine\ElementVisitorInterface;

class Block extends AbstractElement
{
    public function __construct(private readonly AbstractElement $block)
    {
    }

    public function getBlock(): AbstractElement
    {
        return $this->block;
    }

    public function cloneWithBlock(AbstractElement $block): self
    {
        $self = new self($block);
        $this->writeStyle($block);

        return $self;
    }

    /**
     * @template T
     *
     * @param ElementVisitorInterface<T> $visitor
     *
     * @return T
     */
    public function accept(ElementVisitorInterface $visitor): mixed
    {
        return $visitor->visitBlock($this);
    }
}
