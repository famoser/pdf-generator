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

use PdfGenerator\Frontend\Layout\Traits\ColumnStylesTrait;
use PdfGenerator\Frontend\Layout\Traits\GapTrait;
use PdfGenerator\Frontend\Layout\Traits\PerpendicularGapTrait;
use PdfGenerator\Frontend\Layout\Traits\RowsTrait;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

class Grid extends AbstractBlock
{
    use RowsTrait;
    use GapTrait;
    use PerpendicularGapTrait;
    use ColumnStylesTrait;

    public function __construct(float $gap = 0, float $perpendicularGap = 0)
    {
        parent::__construct();
        $this->setGap($gap);
        $this->setPerpendicularGap($perpendicularGap);
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
