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

use PdfGenerator\Frontend\Layout\Traits\BlocksTrait;
use PdfGenerator\Frontend\Layout\Traits\FlowTrait;
use PdfGenerator\Frontend\Layout\Traits\PerpendicularFlowTrait;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

class Grid extends AbstractBlock
{
    use FlowTrait;
    use PerpendicularFlowTrait;
    use BlocksTrait;

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
     * @template T
     *
     * @param AbstractBlockVisitor<T> $visitor
     *
     * @return T
     */
    public function accept(AbstractBlockVisitor $visitor): mixed
    {
        return $visitor->visitGrid($this);
    }
}
