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
use PdfGenerator\Frontend\Layout\Base\BlocksTrait;
use PdfGenerator\Frontend\Layout\Base\FlowTrait;
use PdfGenerator\Frontend\LayoutEngine\AbstractBlockVisitor;

class Flow extends BaseBlock
{
    use FlowTrait;
    use BlocksTrait;

    /**
     * @param float[] $dimensions
     */
    public function __construct(string $direction = self::DIRECTION_ROW, float $gap = 0, array $dimensions = null)
    {
        parent::__construct();
        $this->setDirection($direction);
        $this->setGap($gap);
        $this->setDimensions($dimensions);
    }

    public function accept(AbstractBlockVisitor $visitor): mixed
    {
        return $visitor->visitFlow($this);
    }
}
