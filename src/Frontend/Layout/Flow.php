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

use Famoser\PdfGenerator\Frontend\Content\AbstractContent;
use Famoser\PdfGenerator\Frontend\Layout\Style\FlowDirection;
use Famoser\PdfGenerator\Frontend\LayoutEngine\ElementVisitorInterface;

class Flow extends AbstractElement
{
    /**
     * @var AbstractElement[]
     */
    private array $blocks = [];

    public function __construct(private readonly FlowDirection $direction = FlowDirection::ROW, private readonly float $gap = 0)
    {
    }

    public function add(AbstractElement $block): self
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
     * @return AbstractElement[]
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * @param AbstractElement[] $blocks
     */
    public function cloneWithBlocks(array $blocks): self
    {
        $self = new self($this->direction, $this->gap);
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
     * @param ElementVisitorInterface<T> $visitor
     *
     * @return T
     */
    public function accept(ElementVisitorInterface $visitor): mixed
    {
        return $visitor->visitFlow($this);
    }
}
