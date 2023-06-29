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
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

class Flow extends AbstractBlock
{
    use FlowTrait;
    use BlocksTrait;

    public function __construct(string $direction = self::DIRECTION_ROW, float $gap = 0)
    {
        parent::__construct();
        $this->setDirection($direction);
        $this->setGap($gap);
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
