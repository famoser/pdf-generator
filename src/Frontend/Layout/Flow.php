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

use PdfGenerator\Frontend\Content\AbstractContent;
use PdfGenerator\Frontend\Layout\Style\FlowDirection;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

class Flow extends AbstractBlock
{
    /**
     * @var Block[]
     */
    private array $blocks = [];

    public function __construct(private readonly FlowDirection $direction = FlowDirection::ROW, private readonly float $gap = 0)
    {
    }

    public function add(AbstractBlock $block): self
    {
        $this->blocks[] = $block;

        return $this;
    }

    public function addContent(AbstractContent $content): self
    {
        $this->blocks[] = new ContentBlock($content);

        return $this;
    }

    /**
     * @return Block[]
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * @param AbstractBlock[] $blocks
     */
    public function cloneWithBlocks(array $blocks): self
    {
        $self = clone $this;
        $self->blocks = $blocks;

        return $self;
    }

    public function getDirection(): FlowDirection
    {
        return $this->direction;
    }

    public function getGap(): float
    {
        return $this->gap;
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
        return $visitor->visitFlow($this);
    }
}
