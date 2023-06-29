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

use PdfGenerator\Frontend\Layout\Traits\FlowTrait;
use PdfGenerator\Frontend\Layout\Traits\PerpendicularFlowTrait;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

class Table extends AbstractBlock
{
    use FlowTrait;
    use PerpendicularFlowTrait;

    /**
     * @var Block[][]
     */
    private array $header = [];

    /**
     * @var Block[][]
     */
    private array $body = [];

    public function __construct(string $direction = self::DIRECTION_ROW, float $gap = 0, float $perpendicularGap = 0)
    {
        parent::__construct();
        $this->setDirection($direction);
        $this->setGap($gap);
        $this->setPerpendicularGap($perpendicularGap);
    }

    /**
     * @param Block[] $blocks
     */
    public function addHeader(array $blocks): self
    {
        $this->header[] = $blocks;

        return $this;
    }

    /**
     * @param Block[] $blocks
     */
    public function addBody(array $blocks): self
    {
        $this->body[] = $blocks;

        return $this;
    }

    /**
     * @return Block[]
     */
    public function getHeader(): array
    {
        return $this->header;
    }

    /**
     * @return Block[]
     */
    public function getBody(): array
    {
        return $this->body;
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
        return $visitor->visitTable($this);
    }
}
