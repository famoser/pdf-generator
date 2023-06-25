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
use PdfGenerator\Frontend\Layout\Base\FlowTrait;
use PdfGenerator\Frontend\Layout\Base\PerpendicularFlowTrait;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

class Table extends BaseBlock
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

    /**
     * @param float[]|null $perpendicularDimensions
     * @param float[]      $dimensions
     */
    public function __construct(string $direction = self::DIRECTION_ROW, float $gap = 0, float $perpendicularGap = 0, array $dimensions = null, array $perpendicularDimensions = null)
    {
        parent::__construct();
        $this->setDirection($direction);
        $this->setGap($gap);
        $this->setPerpendicularGap($perpendicularGap);
        $this->setDimensions($dimensions);
        $this->setPerpendicularDimensions($perpendicularDimensions);
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
